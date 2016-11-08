function inArray(needle, haystack) {
  for(var i in haystack) {
    if(haystack[i] == needle)
      return true
  }
  return false;
}

function inArrayKeys(needle, haystack) {
  for(var i in haystack) {
    if(i == needle)
      return true
  }
  return false;
}

function uc_first(str) {
  return str.charAt(0).toUpperCase()+str.slice(1);
}

function valid_date(date) {
  // format: ##/##/####
  format = /^\d{2}\/\d{2}\/\d{4}$/;
  return (date.match(format)) ? true : false;
}

function valid_sql_date(date) {
  // format: ####-##-##
  format = /^\d{4}-\d{2}-\d{2}$/;
  return (date.match(format)) ? true : false;
}

function num_to_money(number) {
  return '$'+number.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

function money_to_num(string) {
  return parseFloat(string.replace('$', '').replace(',', ''));
}

function get_date(date) {
  date = date.trim();
  if(date.charAt(date.length - 1) == '.')
    date = date.substr(0, date.length - 1);
  var day, month, year;
  if(date.match(/jan/i))
    month = 0;
  else if(date.match(/feb/i))
    month = 1;
  else if(date.match(/mar/i))
    month = 2;
  else if(date.match(/apr/i))
    month = 3;
  else if(date.match(/may/i))
    month = 4;
  else if(date.match(/jun/i))
    month = 5;
  else if(date.match(/jul/i))
    month = 6;
  else if(date.match(/aug/i))
    month = 7;
  else if(date.match(/sep/i))
    month = 8;
  else if(date.match(/oct/i))
    month = 9;
  else if(date.match(/nov/i))
    month = 10;
  else if(date.match(/dec/i))
    month = 11;

  // format1: dd-dd-dd, dd/dd/dd (month, day, year)
  format1 = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{2,4})$/;
  // format2: dd-DDD-dd, dd/DDD/dd (day, month, year)
  format2 = /^(\d{1,2})(\/|-)(\D{3})(\/|-)(\d{2,4})$/;
  // format3: 'DDD, dd, dd', 'DDD-d-dddd' (month, day, year)
  format3 = /^(\D{3})(.{1,2})(\d{1,2})(.{1,2})(\d{2,4})$/;

  if(date.match(format1)) {
    day = date.replace(format1, '$3');
    day = (day.length > 1) ? day : '0'+day;
    month = date.replace(format1, '$1');
    month = (month.length > 1) ? month : '0'+month;
    year = date.replace(format1, '$5');
    year = (year.length > 2) ? year : parseInt(year) + 2000;
    date = month+'/'+day+'/'+year;
    // console.log('format1, day: '+day+', month: '+month+', year: '+year+', date: '+date);
  } else if(date.match(format2)) {
    day = date.replace(format2, '$1');
    day = (day.length > 1) ? day : '0'+day;
    month += 1;
    month = (month > 9) ? month : '0'+month;
    year = date.replace(format2, '$5');
    year = (year.length > 2) ? year : parseInt(year) + 2000;
    date = month+'/'+day+'/'+year;
    // console.log('format2, day: '+day+', month: '+month+', year: '+year+', date: '+date);
  } else if(date.match(format3)) {
    day = date.replace(format3, '$3');
    day = (day.length > 1) ? day : '0'+day;
    month += 1;
    month = (month < 10) ? '0'+month : month;
    year = date.replace(format3, '$5');
    year = (year.length > 2) ? year : parseInt(year) + 2000;
    date = month+'/'+day+'/'+year;
    // console.log('format3, day: '+day+', month: '+month+', year: '+year+', date: '+date);
  } else {
    date = '';
  }
  return date;
}

function obj_size(obj) {
  counter = 0;
  for(var i in obj)
    counter++;
  return counter;
}
