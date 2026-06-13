/**
 * Razak Homestay KL - Phase 2 booking backend (Option A).
 * Live availability with owner-confirmed lock.
 *
 * The Google Sheet acts as both the availability record and the booking log.
 *   doGet  -> returns the booked date ranges for each unit.
 *   doPost -> writes a new "Pending" booking row.
 *
 * Security notes (in line with the project rules):
 *   - This script does not use or store any ToyyibPay secret key. Option A
 *     relies on the ToyyibPay hosted link only.
 *   - If you later move to Option B and need a secret key, store it in the
 *     Apps Script "Script Properties", never in this file and never in the HTML.
 */

var SHEET_NAME = 'Bookings';
var PENDING_HOLD_HOURS = 48; // A "Pending" row stops blocking dates after this many hours.
var HEADERS = ['Timestamp', 'Unit', 'CheckIn', 'CheckOut', 'Nights', 'GuestName', 'WhatsApp', 'Status', 'Ref'];

var UNITS = {
  maxim: 'The Maxim Residence - Cheras',
  klcc: 'The Skyline Suite - KLCC'
};

/* ---------------------------------------------------------------------------
   Sheet helpers
--------------------------------------------------------------------------- */

function getSheet_() {
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var sheet = ss.getSheetByName(SHEET_NAME);
  if (!sheet) {
    sheet = ss.insertSheet(SHEET_NAME);
    sheet.appendRow(HEADERS);
    sheet.setFrozenRows(1);
  }
  return sheet;
}

function readRows_() {
  var sheet = getSheet_();
  var lastRow = sheet.getLastRow();
  if (lastRow < 2) return [];
  var values = sheet.getRange(2, 1, lastRow - 1, HEADERS.length).getValues();
  return values.map(function (r) {
    return {
      timestamp: r[0],
      unit: r[1],
      checkIn: r[2],
      checkOut: r[3],
      nights: r[4],
      guestName: r[5],
      whatsapp: r[6],
      status: r[7],
      ref: r[8]
    };
  });
}

function isoDate_(value) {
  if (value instanceof Date) {
    return Utilities.formatDate(value, Session.getScriptTimeZone(), 'yyyy-MM-dd');
  }
  return String(value).slice(0, 10);
}

/* ---------------------------------------------------------------------------
   Availability
--------------------------------------------------------------------------- */

function buildAvailability_() {
  var rows = readRows_();
  var now = new Date();
  var result = { maxim: [], klcc: [] };

  rows.forEach(function (row) {
    var unit = String(row.unit || '').toLowerCase();
    if (!result[unit]) return;

    var status = String(row.status || '').toLowerCase();
    var blocks = false;

    if (status === 'paid') {
      blocks = true;
    } else if (status === 'pending') {
      var created = row.timestamp instanceof Date ? row.timestamp : new Date(row.timestamp);
      if (created && (now - created) / 3600000 <= PENDING_HOLD_HOURS) {
        blocks = true;
      }
    }

    if (blocks && row.checkIn && row.checkOut) {
      result[unit].push([isoDate_(row.checkIn), isoDate_(row.checkOut)]);
    }
  });

  return result;
}

/* ---------------------------------------------------------------------------
   Responses (supports JSONP so the static site can read it without CORS issues)
--------------------------------------------------------------------------- */

function jsonResponse_(obj, callback) {
  var json = JSON.stringify(obj);
  if (callback) {
    return ContentService
      .createTextOutput(callback + '(' + json + ');')
      .setMimeType(ContentService.MimeType.JAVASCRIPT);
  }
  return ContentService
    .createTextOutput(json)
    .setMimeType(ContentService.MimeType.JSON);
}

/* ---------------------------------------------------------------------------
   Web app entry points
--------------------------------------------------------------------------- */

function doGet(e) {
  var callback = e && e.parameter ? e.parameter.callback : null;
  try {
    return jsonResponse_({ ok: true, availability: buildAvailability_() }, callback);
  } catch (err) {
    return jsonResponse_({ ok: false, error: String(err) }, callback);
  }
}

function overlaps_(aIn, aOut, bIn, bOut) {
  // YYYY-MM-DD strings compare correctly in lexical order.
  return aIn < bOut && bIn < aOut;
}

function doPost(e) {
  var lock = LockService.getScriptLock();
  try {
    lock.waitLock(10000);

    var data = JSON.parse((e && e.postData && e.postData.contents) || '{}');
    var unit = String(data.unit || '').toLowerCase();
    var checkIn = isoDate_(data.checkIn || '');
    var checkOut = isoDate_(data.checkOut || '');

    if (!UNITS[unit]) return jsonResponse_({ ok: false, error: 'invalid_unit' });
    if (!/^\d{4}-\d{2}-\d{2}$/.test(checkIn) || !/^\d{4}-\d{2}-\d{2}$/.test(checkOut)) {
      return jsonResponse_({ ok: false, error: 'invalid_dates' });
    }
    if (checkOut <= checkIn) return jsonResponse_({ ok: false, error: 'bad_range' });

    var todayIso = isoDate_(new Date());
    if (checkIn < todayIso) return jsonResponse_({ ok: false, error: 'past_date' });

    // Re-check availability on the server to guard against a double booking.
    var blocked = buildAvailability_()[unit] || [];
    var clash = blocked.some(function (range) {
      return overlaps_(checkIn, checkOut, range[0], range[1]);
    });
    if (clash) return jsonResponse_({ ok: false, error: 'unavailable' });

    var nights = Math.round((new Date(checkOut) - new Date(checkIn)) / 86400000);
    var guestName = String(data.guestName || '').slice(0, 120);
    var whatsapp = String(data.whatsapp || '').slice(0, 40);
    var ref = String(data.ref || '').slice(0, 40) ||
      ('RHK-' + unit.toUpperCase() + '-' + Date.now().toString(36).toUpperCase());

    getSheet_().appendRow([
      new Date(), unit, checkIn, checkOut, nights, guestName, whatsapp, 'Pending', ref
    ]);

    return jsonResponse_({ ok: true, ref: ref, nights: nights });
  } catch (err) {
    return jsonResponse_({ ok: false, error: String(err) });
  } finally {
    try { lock.releaseLock(); } catch (ignore) {}
  }
}
