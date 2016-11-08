<?php
$root = $_SERVER['DOCUMENT_ROOT'];
include_once $root.'/db/db_connect.php';
include_once $root.'/login/functions.php';
sec_session_start();
if (login_check($mysqli) == false)
  header('Location: /login/login.php?error=1');
$user_id = $_SESSION['user_id'];

?>

<html>
  <head>
    <link href="/includes/bootstrap-3.1.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/includes/bootstrap-3.1.1/js/bootstrap.min.js"></script>
    <script src="/js/sha512.js" type="text/JavaScript"></script>
    <script src="/js/forms.js" type="text/JavaScript"></script>
    <!-- needed for ajax request -->
    <script src="http://malsup.github.com/jquery.form.js"></script>
    <!-- choose a theme file -->
    <link rel="stylesheet" href="/includes/tablesorter/css/theme.default.css">
    <!-- load jQuery and tablesorter scripts -->
    <!-- <script type="text/javascript" src="/path/to/jquery-latest.js"></script> -->
    <script type="text/javascript" src="/includes/tablesorter/js/jquery.tablesorter.js"></script>
    <!-- tablesorter widgets (optional) -->
    <link href="/includes/bootstrap-3.1.1/css/datepicker.css" rel="stylesheet">
    <script type="text/javascript" src="/includes/tablesorter/js/jquery.tablesorter.widgets.js"></script>
    <script type="text/javascript" src="/includes/tablesorter/addons/pager/jquery.tablesorter.pager.js"></script>
    <script src="/includes/bootstrap-3.1.1/js/bootstrap-datepicker.js" type="text/javascript"></script>
  </head>
  <body>
    <?php require_once $root.'/template/header.php' ?>
    <div class="container" style="width:90%;text-align:center">
     <div id="alert"></div>
     <h2>Browse Contacts</h2>
      <div class="jumbotron" align="center">
        <table id="controls" class="form-inline">
          <tr>
            <td>
              <label>First</label>
              <input type="text" class="form-control" id="first" maxlength="10" onkeypress="checkSubmit(event)">
            </td>
            <td>
              <label>Last</label>
              <input type="text" class="form-control" id="last" maxlength="10" onkeypress="checkSubmit(event)">
            </td>
            <td>
              <label>Title</label>
              <input type="text" class="form-control" id="title" onkeypress="checkSubmit(event)">
            </td>
            <td>
              <label>Location</label>
              <input type="text" class="form-control" id="location" onkeypress="checkSubmit(event)">
            </td>
          </tr>
          <tr>
            <td>
              <label>Email</label>
              <input type="text" class="form-control" id="email" maxlength="10" onkeypress="checkSubmit(event)">
            </td>
            <td>
              <label>Phone</label>
              <input type="text" class="form-control" id="phone" onkeypress="checkSubmit(event)">
            </td>
            <td>
              <label>Hourly</label>
              <select class="form-control" id="hourly_comp">
                <option value="=">=</option>
                <option value="<"><</option>
                <option value=">">></option>
              </select>
              <input type="text" class="form-control" id="hourly" style="width:60%" onkeypress="checkSubmit(event)">
            </td>
            <td>
              <label>Daily</label>
              <select class="form-control" id="daily_comp">
                <option value="=">=</option>
                <option value="<"><</option>
                <option value=">">></option>
              </select>
              <input type="text" class="form-control" id="daily" style="width:60%" onkeypress="checkSubmit(event)">
            </td>
          </tr>
          <tr>
            <td>
              <label>URL</label>
              <input type="text" class="form-control" id="url" onkeypress="checkSubmit(event)">
            </td>
            <td>
              <label>Comments</label>
              <textarea id="comments" class="form-control"></textarea>
            </td>
          </tr>
          <tr>
            <td colspan="4">
              <input type="button" id="reset" value="Reset" onclick="reset_filters()" class="btn btn-primary"/>
              <input type="button" id="search" value="Search" onclick="pull_contacts()" class="btn btn-primary"/>
            </td>
          </tr>
        </table><br />
      </div>
      <div class="jumbotron" align="center" id="resultsContainer" style="display:none">
        <table id="results" name="results" class="tablesorter"></table>
      </div>
    </div>

  </body>
</html>

<script>
  $(document).ready(function() {

  });

  function delete_contact(contact_id) {
    if (confirm('Are you sure you want to delete the contact?')) {
      var dt = {};
      dt['action'] = 'delete_contact';
      dt['contact_id'] = contact_id;
      $.ajax({
        type: 'POST',
        url: 'contacts_ajax.php',
        data: dt,
        dataType: 'json',
        async: false,
        success: function(json) {
          // console.log('browse_contacts.php delete_contact() ajax success');
          // console.log(json);
          data = json.data;
        },
        error: function(json) {
          console.log('browse_contacts.php delete_contact() ajax error');
          console.log(json);
        }
      });
    }
    pull_contacts();
  }

  function pull_contacts() {
    var dt = {};
    dt['action'] = 'pull_contacts';

    dt['filters'] = {};
    dt['comp'] = {};
    dt['filters']['first'] = $('#first').val();
    dt['filters']['last'] = $('#last').val();
    dt['filters']['email'] = $('#email').val();
    dt['filters']['phone'] = $('#phone').val();
    dt['filters']['location'] = $('#location').val();
    dt['filters']['title'] = $('#title').val();
    dt['filters']['hourly'] = $('#hourly').val();
    dt['comp']['hourly'] = $('#hourly_comp').val();
    dt['filters']['daily'] = $('#daily').val();
    dt['comp']['daily'] = $('#daily_comp').val();
    dt['filters']['url'] = $('#url').val();
    dt['filters']['comments'] = $('#comments').val();

    $('#resultsContainer').show();
    $('#results tbody').html('');
    $('#results').trigger("destroy");
    $('#results').html('');
    var data = {};
    $.ajax({
      type: 'POST',
      url: 'contacts_ajax.php',
      data: dt,
      dataType: 'json',
      async: false,
      success: function(json) {
        // console.log('browse_contacts.php pull_contacts() ajax success');
        // console.log(json);
        data = json.data;
      },
      error: function(json) {
        console.log('browse_contacts.php pull_contacts() ajax error');
        console.log(json);
      }
    });
    var tbody = '';
    for(var i in data) {
      if (i != 'fields') {
        var id = data[i].id;
        tbody += "<tr>";
          tbody += "<td><a href='contact.php?id="+id+"' target='_blank'>"+id+"</a></td>";
          tbody += "<td>"+data[i].first+"</td>";
          tbody += "<td>"+data[i].last+"</td>";

          if (data[id]['fields']) {
            tbody += "<td>";
            if (data[id]['fields']['email']) {
              for(var j in data[id]['fields']['email']) {
                for(var k in data[id]['fields']['email'][j])
                  tbody += "<div>"+k.charAt(0).toUpperCase()+k.slice(1)+'<br />'+data[id]['fields']['email'][j][k]+"</div>";
              }
            }
            tbody += "</td>";
            tbody += "<td>";
            if (data[id]['fields']['phone']) {
              for(var j in data[id]['fields']['phone']) {
                for(var k in data[id]['fields']['phone'][j])
                  tbody += "<div>"+k.charAt(0).toUpperCase()+k.slice(1)+'<br />'+data[id]['fields']['phone'][j][k]+"</div>";
              }
            }
            tbody += "</td>";
            tbody += "<td>"+data[i].location+"</td>";
            tbody += "<td>";
            if (data[id]['fields']['url']) {
              for(var j in data[id]['fields']['url']) {
                for(var k in data[id]['fields']['url'][j])
                  tbody += "<div>"+k.charAt(0).toUpperCase()+k.slice(1)+'<br />'+data[id]['fields']['url'][j][k]+"</div>";
              }
            }
            tbody += "</td>";
          } else {
            tbody += "<td></td>";
            tbody += "<td></td>";
            tbody += "<td style='width:8%'>"+data[i].location+"</td>";
            tbody += "<td></td>";
          }

          tbody += "<td>"+data[i].title+"</td>";
          tbody += "<td>"+data[i].hourly+"</td>";
          tbody += "<td>"+data[i].daily+"</td>";
          tbody += "<td><div style='overflow-y:scroll;resize:vertical;text-align:left;height:40px'>"+data[i].comments+"</div></td>";
          tbody += "<td style='vertical-align:middle'>";
            tbody += "<input type='button' value='-' onclick='delete_contact("+id+")' class='btn btn-sm btn-danger'>";
          tbody += "</td>";
        tbody += "</tr>";
      }
    }
    var tablesorter_options = {
      sortList: [ [3,1] ],
      widthFixed: true,
      sortLocaleCompare: true, // needed for accented characters in the data
      widgets: ['zebra', 'filter', 'stickyHeaders', 'uitheme'],

      // headerTemplate : '{content}{icon}',
      // widgetOptions: {
      //   filter_saveFilters: true,
      //   filter_reset: 'button.reset',
      //   filter_formatter: {
      //     // 3: function($cell, indx) {
      //     //   return $.tablesorter.filterFormatter.uiDatepicker( $cell, indx, {
      //     //     // from : '08/01/2013', // default from date
      //     //     // to   : '1/18/2014',  // default to date
      //     //     changeMonth : true,
      //     //     changeYear : true
      //     //   });
      //     // }
      //     0: function($cell, indx) {
      //       return $.tablesorter.filterFormatter.uiSpinner( $cell, indx, {
      //         delayed : true,
      //         addToggle : false,
      //         exactMatch : true,
      //         compare : [ '', '=', '>=', '<=' ],
      //         selected : 2,
      //         // jQuery UI spinner options
      //         min : 0,
      //         max : 45,
      //         value : 1,
      //         step : 1
      //       });
      //     }
      //   }
      // },
      // filter_placeholder : {
      //   from : 'From...',
      //   to   : 'To...'
      // }

    }

    $('#results').html(draw_header());
    $('#results tbody').html(tbody);
    $('#results').tablesorter(tablesorter_options).tablesorterPager({
      // **********************************
      //  Description of ALL pager options
      // **********************************

      // target the pager markup - see the HTML block below
      container: $(".pager"),

      // use this format: "http:/mydatabase.com?page={page}&size={size}&{sortList:col}"
      // where {page} is replaced by the page number (or use {page+1} to get a one-based index),
      // {size} is replaced by the number of records to show,
      // {sortList:col} adds the sortList to the url into a "col" array, and {filterList:fcol} adds
      // the filterList to the url into an "fcol" array.
      // So a sortList = [[2,0],[3,0]] becomes "&col[2]=0&col[3]=0" in the url
      // and a filterList = [[2,Blue],[3,13]] becomes "&fcol[2]=Blue&fcol[3]=13" in the url
      ajaxUrl : null,

      // modify the url after all processing has been applied
      // customAjaxUrl: function(table, url) {
      //     // manipulate the url string as you desire
      //     // url += '&cPage=' + window.location.pathname;
      //     // trigger my custom event
      //     $(table).trigger('changingUrl', url);
      //     // send the server the current page
      //     return url;
      // },

      // add more ajax settings here
      // see http://api.jquery.com/jQuery.ajax/#jQuery-ajax-settings
      ajaxObject: {
        dataType: 'json'
      },

      // process ajax so that the following information is returned:
      // [ total_rows (number), rows (array of arrays), headers (array; optional) ]
      // example:
      // [
      //   100,  // total rows
      //   [
      //     [ "row1cell1", "row1cell2", ... "row1cellN" ],
      //     [ "row2cell1", "row2cell2", ... "row2cellN" ],
      //     ...
      //     [ "rowNcell1", "rowNcell2", ... "rowNcellN" ]
      //   ],
      //   [ "header1", "header2", ... "headerN" ] // optional
      // ]
      // OR
      // return [ total_rows, $rows (jQuery object; optional), headers (array; optional) ]
      ajaxProcessing: function(data){
        if (data && data.hasOwnProperty('rows')) {
          var r, row, c, d = data.rows,
          // total number of rows (required)
          total = data.total_rows,
          // array of header names (optional)
          headers = data.headers,
          // all rows: array of arrays; each internal array has the table cell data for that row
          rows = [],
          // len should match pager set size (c.size)
          len = d.length;
          // this will depend on how the json is set up - see City0.json
          // rows
          for ( r=0; r < len; r++ ) {
            row = []; // new row array
            // cells
            for ( c in d[r] ) {
              if (typeof(c) === "string") {
                row.push(d[r][c]); // add each table cell data to row array
              }
            }
            rows.push(row); // add new row array to rows array
          }
          // in version 2.10, you can optionally return $(rows) a set of table rows within a jQuery object
          return [ total, rows, headers ];
        }
      },

      // output string - default is '{page}/{totalPages}'; possible variables: {page}, {totalPages}, {startRow}, {endRow} and {totalRows}
      output: '{startRow} to {endRow} ({totalRows})',

      // apply disabled classname to the pager arrows when the rows at either extreme is visible - default is true
      updateArrows: true,

      // starting page of the pager (zero based index)
      page: 0,

      // Number of visible rows - default is 10
      size: 25,

      // if true, the table will remain the same height no matter how many records are displayed. The space is made up by an empty
      // table row set to a height to compensate; default is false
      fixedHeight: false,

      // remove rows from the table to speed up the sort of large tables.
      // setting this to false, only hides the non-visible rows; needed if you plan to add/remove rows with the pager enabled.
      removeRows: false,

      // css class names of pager arrows
      cssNext        : '.next',  // next page arrow
      cssPrev        : '.prev',  // previous page arrow
      cssFirst       : '.first', // go to first page arrow
      cssLast        : '.last',  // go to last page arrow
      cssPageDisplay : '.pagedisplay', // location of where the "output" is displayed
      cssPageSize    : '.pagesize', // page size selector - select dropdown that sets the "size" option
      cssErrorRow    : 'tablesorter-errorRow', // error information row

      // class added to arrows when at the extremes (i.e. prev/first arrows are "disabled" when on the first page)
      cssDisabled    : 'disabled' // Note there is no period "." in front of this class name
    });
    $('#results').show();
    $('.pager').show('slow');
  }

  function reset_filters() {
    $('#first').val('');
    $('#last').val('');
    $('#email').val('');
    $('#phone').val('');
    $('#location').val('');
    $('#url').val('');
    $('#title').val('');
    $('#hourly_comp').val('=');
    $('#hourly').val('');
    $('#daily_comp').val('=');
    $('#daily').val('');
    $('#comments').val('');
    $('#resultsContainer').hide();
  }

  function draw_header() {
    var t = "<thead>";
      t += "<tr>";
        t += "<td class='pager' colspan='12'>";
          t += "<img src='/includes/tablesorter/addons/pager/icons/first.png' class='first' />";
          t += "<img src='/includes/tablesorter/addons/pager/icons/prev.png' class='prev' />";
          t += "<span class='pagedisplay'></span>"; // <!-- this can be any element, including an input -->
          t += "<img src='/includes/tablesorter/addons/pager/icons/next.png' class='next'/>";
          t += "<img src='/includes/tablesorter/addons/pager/icons/last.png' class='last'/>";
          t += "<select class='pagesize'>";
            t += "<option value='25'>25</option>";
            t += "<option value='50'>50</option>";
            t += "<option value='100'>100</option>";
            t += "<option value='1000'>1000</option>";
          t += "</select>";
        t += "</td>";
      t += "</tr>";
      t += "<tr>";
        t += "<th>ID</th>";
        t += "<th>First</th>";
        t += "<th>Last</th>";
        t += "<th>Email</th>";
        t += "<th>Phone</th>";
        t += "<th>Location</th>";
        t += "<th>URL</th>";
        t += "<th>Title</th>";
        t += "<th>Hourly</th>";
        t += "<th>Daily</th>";
        t += "<th>Comments</th>";
        t += "<th></th>";
      t += "</tr>";
    t += "</thead>";
    t += "<tfoot>";
      t += "<tr>";
        t += "<th>ID</th>";
        t += "<th>First</th>";
        t += "<th>Last</th>";
        t += "<th>Email</th>";
        t += "<th>Phone</th>";
        t += "<th>Location</th>";
        t += "<th>URL</th>";
        t += "<th>Title</th>";
        t += "<th>Hourly</th>";
        t += "<th>Daily</th>";
        t += "<th>Comments</th>";
        t += "<th></th>";
      t += "</tr>";
      t += "<tr>";
        t += "<td class='pager' colspan='12'>";
          t += "<img src='/includes/tablesorter/addons/pager/icons/first.png' class='first' />";
          t += "<img src='/includes/tablesorter/addons/pager/icons/prev.png' class='prev' />";
          t += "<span class='pagedisplay'></span>"; // <!-- this can be any element, including an input -->
          t += "<img src='/includes/tablesorter/addons/pager/icons/next.png' class='next'/>";
          t += "<img src='/includes/tablesorter/addons/pager/icons/last.png' class='last'/>";
          t += "<select class='pagesize'>";
            t += "<option value='25'>25</option>";
            t += "<option value='50'>50</option>";
            t += "<option value='100'>100</option>";
            t += "<option value='1000'>1000</option>";
          t += "</select>";
        t += "</td>";
      t += "</tr>";
    t += "</tfoot>";
    t += "<tbody>";
    t += "</tbody>";
    return t;
  }

  function checkdate(date) {
    format = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
    return date.match(format) ? true : false;
  }

  function checkSubmit(e) {
    if(e && e.keyCode == 13) {
      pull_contacts();
    }
  }

</script>

<style>
/* pager wrapper, div */
.pager {
  padding: 5px;
}
/* pager wrapper, in thead/tfoot */
td.pager {
  background-color: #e6eeee;
}
/* pager navigation arrows */
.pager img {
  vertical-align: middle;
  margin-right: 2px;
}
/* pager output text */
.pager .pagedisplay {
  font-size: 11px;
  padding: 0 5px 0 5px;
  width: 50px;
  text-align: center;
}

/*** loading ajax indeterminate progress indicator ***/
#tablesorterPagerLoading {
  background: rgba(255,255,255,0.8) url(icons/loading.gif) center center no-repeat;
  position: absolute;
  z-index: 1000;
}

/*** css used when "updateArrows" option is true ***/
/* the pager itself gets a disabled class when the number of rows is less than the size */
.pager.disabled {
  display: none;
}
/* hide or fade out pager arrows when the first or last row is visible */
.pager img.disabled {
  /* visibility: hidden */
  opacity: 0.5;
  filter: alpha(opacity=50);
}
#controls td {
  padding: 10px;
  text-align: center;
  width: 25%;
}
#controls label {
  display: block;
}
#results table {
  text-align: left;
}
#results td {
  text-align: center;
}
#results div {
  padding: 2px;
}
</style>