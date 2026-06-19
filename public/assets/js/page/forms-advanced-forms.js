"use strict";

function cleaveInit(selector, opts) {
  if (document.querySelector(selector)) {
    return new Cleave(selector, opts);
  }
}

cleaveInit('.phone-number',  { phone: true, phoneRegionCode: 'us' });
cleaveInit('.currency',      { numeral: true, numeralThousandsGroupStyle: 'thousand' });
cleaveInit('.purchase-code', { delimiter: '-', blocks: [4, 4, 4, 4], uppercase: true });
cleaveInit('.invoice-input', { prefix: 'INV', delimiter: '-', blocks: [10], uppercase: true });
cleaveInit('.datemask',      { date: true, datePattern: ['Y', 'm', 'd'] });

if (document.querySelector('.creditcard')) {
  var cc_last_type;
  new Cleave('.creditcard', {
    creditCard: true,
    onCreditCardTypeChanged: function(type) {
      if (type !== 'unknown') {
        if (type === 'amex') type = 'americanexpress';
        else if (type === 'diners') type = 'dinersclub';
        $(".creditcard").removeClass(cc_last_type).addClass(type);
        cc_last_type = type;
      }
    }
  });
}

if (document.querySelector('.pwstrength')) {
  $(".pwstrength").pwstrength();
}

if (document.querySelector('.daterange-cus')) {
  $('.daterange-cus').daterangepicker({
    locale: { format: 'YYYY-MM-DD' },
    drops: 'down',
    opens: 'right'
  });
}

if (document.querySelector('.daterange-btn')) {
  $('.daterange-btn').daterangepicker({
    ranges: {
      'Today'       : [moment(), moment()],
      'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
      'Last 30 Days': [moment().subtract(29, 'days'), moment()],
      'This Month'  : [moment().startOf('month'), moment().endOf('month')],
      'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    startDate: moment().subtract(29, 'days'),
    endDate  : moment()
  }, function(start, end) {
    $('.daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
  });
}

if (document.querySelector('.colorpickerinput')) {
  $(".colorpickerinput").colorpicker({ format: 'hex', component: '.input-group-append' });
}

if (document.querySelector('.inputtags')) {
  $(".inputtags").tagsinput('items');
}
