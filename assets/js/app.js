$('.tip').tooltip();
$('.sort').tablesorter();
$(function () {
    $('#event-start').datetimepicker({
      format: "YYYY-MM-DD HH:mm",
      viewMode: "months",
      useCurrent: false,
      sideBySide: true
    });
    $('#event-end').datetimepicker({
      format: "YYYY-MM-DD HH:mm",
      viewMode: "months",
      useCurrent: false,
      sideBySide: true
    });
    $("#event-start").on("dp.change",function (e) {
        $('#event-end').data("DateTimePicker").minDate(e.date);
    });
    $("#event-end").on("dp.change",function (e) {
        $('#event-start').data("DateTimePicker").maxDate(e.date);
    });
});

$(function () {
    var start = $('#shift-start').attr('data-event-start');
    $('#shift-start').datetimepicker({
      format: "YYYY-MM-DD HH:mm",
      viewMode: "days",
      useCurrent: false,
      defaultDate: start
    });
    $('#shift-end').datetimepicker({
      format: "YYYY-MM-DD HH:mm",
      viewMode: "days",
      useCurrent: false,
      defaultDate: start
    });
    $("#shift-start").on("dp.change",function (e) {
        $('#shift-end').data("DateTimePicker").minDate(e.date);
    });
    $("#shift-end").on("dp.change",function (e) {
        $('#shift-start').data("DateTimePicker").maxDate(e.date);
    });
});


$('.color-choice').click(function(e){
  var bg = $(this).attr('data-color');
  var textcolor = $(this).attr('data-text');
  $(this).toggleClass('color-active');
  $('#badge-preview').css('background-color',bg);
  $('#badge-preview').css('color',textcolor);
  $('#color').val(bg);
  $('#color2').val(textcolor);
});

$('#name').keyup(function(){
  var text = $(this).val();
  $('#badge-text').html(text);
});

$('#desc').keyup(function(){
  var text = $(this).val();
  $('#badge-text').attr('title',text);
});

$('#icon').keyup(function(){
  var icon = $(this).val();
  if($.inArray(icon, icons.icons) > -1) {
    $('#badge-icon').removeClass().addClass('fa').addClass('fa-'+icon);
  }
});