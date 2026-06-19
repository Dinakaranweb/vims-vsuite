"use strict";

$("#swal-1").click(function() {
	swal('Hello');
});

$("#swal-2").click(function() {
	swal('Good Job', 'You clicked the button!', 'success');
});

$("#swal-3").click(function() {
	swal('Good Job', 'You clicked the button!', 'warning');
});

$(".table-links a[href='#']").click(function(e) {
  e.preventDefault();
  var ticketId = $(this).closest("tr").data("id");
  // Make the AJAX request to retrieve the activity log data for the selected ticket
  $.ajax({
    url: '/activity-log/' + ticketId,
    method: 'GET',
    success: function(data) {
      
      var htmlContent = '';
      data.forEach(function(item) {
        htmlContent += `
          <div class="a-s--item" style="padding: 10px; border-bottom: 1px solid #ddd;">
            <div class="a-s--body">
              <p style="margin-bottom: 10px; font-size: 14px; color: #000;">${item.description}</p>
            </div>
          </div><!-- /.a-s--item -->
        `;
      });

      var container = document.createElement('div');
      container.innerHTML = htmlContent;

      swal({
        title: 'Activity Log',
        content: container, // Set the content to the created container
        //icon: 'info'
      });
    }
  });
});


$("#swal-5").click(function() {
	swal('Good Job', 'You clicked the button!', 'error');
});

$("#swal-6").click(function() {
  swal({
      title: 'Are you sure?',
      text: 'Once deleted, you will not be able to recover this imaginary file!',
      icon: 'warning',
      buttons: true,
      dangerMode: true,
    })
    .then((willDelete) => {
      if (willDelete) {
      swal('Poof! Your imaginary file has been deleted!', {
        icon: 'success',
      });
      } else {
      swal('Your imaginary file is safe!');
      }
    });
});

$("#swal-7").click(function() {
  swal({
    title: 'What is your name?',
    content: {
    element: 'input',
    attributes: {
      placeholder: 'Type your name',
      type: 'text',
    },
    },
  }).then((data) => {
    swal('Hello, ' + data + '!');
  });
});

$("#swal-8").click(function() {
  swal('This modal will disappear soon!', {
    buttons: false,
    timer: 3000,
  });
});