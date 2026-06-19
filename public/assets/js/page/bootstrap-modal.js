"use strict";

$("#modal-1").fireModal({body: 'Modal body text goes here.'});
$("#modal-2").fireModal({body: 'Modal body text goes here.', center: true});

let modal_3_body = '<p>Object to create a button on the modal.</p><pre class="language-javascript"><code>';
modal_3_body += '[\n';
modal_3_body += ' {\n';
modal_3_body += "   text: 'Login',\n";
modal_3_body += "   submit: true,\n";
modal_3_body += "   class: 'btn btn-primary btn-shadow',\n";
modal_3_body += "   handler: function(modal) {\n";
modal_3_body += "     alert('Hello, you clicked me!');\n"
modal_3_body += "   }\n"
modal_3_body += ' }\n';
modal_3_body += ']';
modal_3_body += '</code></pre>';
$("#modal-3").fireModal({
  title: 'Modal with Buttons',
  body: modal_3_body,
  buttons: [
    {
      text: 'Click, me!',
      class: 'btn btn-primary btn-shadow',
      handler: function(modal) {
        alert('Hello, you clicked me!');
      }
    }
  ]
});

$("#modal-4").fireModal({
  footerClass: 'bg-whitesmoke',
  body: 'Add the <code>bg-whitesmoke</code> class to the <code>footerClass</code> option.',
  buttons: [
    {
      text: 'No Action!',
      class: 'btn btn-primary btn-shadow',
      handler: function(modal) {
      }
    }
  ]
});

$("#modal-5").fireModal({
  title: 'Close Ticket',
  body: $("#modal-login-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  shown: function(modal, form) {
    console.log(form)
  },
});

$("#modal-6").fireModal({
  body: '<p>Now you can see something on the left side of the footer.</p>',
  created: function(modal) {
    modal.find('.modal-footer').prepend('<div class="mr-auto"><a href="#">I\'m a hyperlink!</a></div>');
  },
  buttons: [
    {
      text: 'No Action',
      submit: true,
      class: 'btn btn-primary btn-shadow',
      handler: function(modal) {
      }
    }
  ]
});

$('.oh-my-modal').fireModal({
  title: 'My Modal',
  body: 'This is cool plugin!'
});

$("#modal-8").fireModal({
  title: 'Forward',
  body: $("#modal-forward-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
        $(modal).find('.modal-dialog').addClass('modal-lg');
    },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent the default form submission immediately

    $(e.target).find('.summernote, .summernote-simple').each(function() {
      try { $(this).val($(this).summernote('code')); } catch(ex) {}
    });
    let form_data = $(e.target).serialize();

    $.ajax({
      type: 'POST',
      url: '/forward/ticket',
      data: form_data,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');

          // Reload the page after a short delay to allow the message to be visible
          setTimeout(function() {
            location.reload();
          }, 2000); // Delay for 2 seconds
        } else if (response.status === 'error') {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        }
      },
      error: function(xhr) {
        let response = JSON.parse(xhr.responseText);
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
      },
      complete: function() {
        form.stopProgress(); // Stop the loading spinner
      }
    });
  },
  buttons: [
    {
      text: 'Forward',
      submit: true,
      class: 'btn btn-danger btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-approve").fireModal({
  title: 'Approve',
  body: $("#modal-approve-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
        $(modal).find('.modal-dialog').addClass('modal-lg');
    },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent the default form submission immediately

    // Disable the Approve button
    let approveButton = modal.find('.btn-success');
    approveButton.prop('disabled', true);

    $(e.target).find('.summernote, .summernote-simple').each(function() {
      try { $(this).val($(this).summernote('code')); } catch(ex) {}
    });
    let form_data = $(e.target).serialize();

    $.ajax({
      type: 'POST',
      url: '/change/document/status',
      data: form_data,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');

          // Reload the page after a short delay to allow the message to be visible
          setTimeout(function() {
            location.reload();
          }, 2000); // Delay for 2 seconds
        } else if (response.status === 'error') {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
          approveButton.prop('disabled', false); // Re-enable on error
        }
      },
      error: function(xhr) {
        let response = JSON.parse(xhr.responseText);
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        approveButton.prop('disabled', false); // Re-enable on error
      },
      complete: function() {
        form.stopProgress(); // Stop the loading spinner
      }
    });
  },
  buttons: [
    {
      text: 'Approve',
      submit: true,
      class: 'btn btn-success btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-acknowledge-stb").fireModal({
  title: 'Acknowledge & Forward',
  body: $("#modal-acknowledge-stb-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
    $(modal).find('.modal-dialog').addClass('modal-lg');
  },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault();

    let ackButton = modal.find('.btn-stb-ack');
    ackButton.prop('disabled', true);

    $(e.target).find('.summernote, .summernote-simple').each(function() {
      try { $(this).val($(this).summernote('code')); } catch(ex) {}
    });
    let form_data = $(e.target).serialize();

    $.ajax({
      type: 'POST',
      url: '/change/document/status',
      data: form_data,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');
          setTimeout(function() {
            location.reload();
          }, 2000);
        } else if (response.status === 'error') {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
          ackButton.prop('disabled', false);
        }
      },
      error: function(xhr) {
        let response = JSON.parse(xhr.responseText);
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        ackButton.prop('disabled', false);
      },
      complete: function() {
        form.stopProgress();
      }
    });
  },
  buttons: [
    {
      text: 'Acknowledge & Forward',
      submit: true,
      class: 'btn btn-primary btn-shadow btn-stb-ack',
      handler: function(modal) {}
    }
  ]
});

$("#modal-approve-in-principle").fireModal({
  title: 'Approve',
  body: $("#modal-approve-in-principle-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
        $(modal).find('.modal-dialog').addClass('modal-lg');
    },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent the default form submission immediately

    // Disable the Approve in Principle button
    let approveBtn = modal.find('.btn-primary');
    approveBtn.prop('disabled', true);

    $(e.target).find('.summernote, .summernote-simple').each(function() {
      try { $(this).val($(this).summernote('code')); } catch(ex) {}
    });
    let form_data = $(e.target).serialize();

    $.ajax({
      type: 'POST',
      url: '/change/document/status',
      data: form_data,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');

          // Reload the page after a short delay to allow the message to be visible
          setTimeout(function() {
            location.reload();
          }, 2000); // Delay for 2 seconds
        } else if (response.status === 'error') {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
          approveBtn.prop('disabled', false); // Re-enable on error
        }
      },
      error: function(xhr) {
        let response = JSON.parse(xhr.responseText);
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        approveBtn.prop('disabled', false); // Re-enable on error
      },
      complete: function() {
        form.stopProgress(); // Stop the loading spinner
      }
    });
  },
  buttons: [
    {
      text: 'Approve in Principle',
      submit: true,
      class: 'btn btn-primary btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-retract").fireModal({
  title: 'Retract',
  body: $("#modal-retract-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
        $(modal).find('.modal-dialog').addClass('modal-lg');
    },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent the default form submission immediately

    // Disable the Approve in Principle button
    let approveBtn = modal.find('.btn-primary');
    approveBtn.prop('disabled', true);

    $(e.target).find('.summernote, .summernote-simple').each(function() {
      try { $(this).val($(this).summernote('code')); } catch(ex) {}
    });
    let form_data = $(e.target).serialize();

    $.ajax({
      type: 'POST',
      url: '/change/document/status',
      data: form_data,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');

          // Reload the page after a short delay to allow the message to be visible
          setTimeout(function() {
            location.reload();
          }, 2000); // Delay for 2 seconds
        } else if (response.status === 'error') {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
          approveBtn.prop('disabled', false); // Re-enable on error
        }
      },
      error: function(xhr) {
        let response = JSON.parse(xhr.responseText);
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        approveBtn.prop('disabled', false); // Re-enable on error
      },
      complete: function() {
        form.stopProgress(); // Stop the loading spinner
      }
    });
  },
  buttons: [
    {
      text: 'Retract',
      submit: true,
      class: 'btn btn-primary btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-hold").fireModal({
  title: 'Hold',
  body: $("#modal-hold-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
        $(modal).find('.modal-dialog').addClass('modal-lg');
    },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent the default form submission immediately

    $(e.target).find('.summernote, .summernote-simple').each(function() {
      try { $(this).val($(this).summernote('code')); } catch(ex) {}
    });
    let form_data = $(e.target).serialize();

    $.ajax({
      type: 'POST',
      url: '/change/document/status',
      data: form_data,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');

          // Reload the page after a short delay to allow the message to be visible
          setTimeout(function() {
            location.reload();
          }, 2000); // Delay for 2 seconds
        } else if (response.status === 'error') {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        }
      },
      error: function(xhr) {
        let response = JSON.parse(xhr.responseText);
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
      },
      complete: function() {
        form.stopProgress(); // Stop the loading spinner
      }
    });
  },
  buttons: [
    {
      text: 'Hold',
      submit: true,
      class: 'btn btn-dark btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-close").fireModal({
  title: 'Close',
  body: $("#modal-close-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
        $(modal).find('.modal-dialog').addClass('modal-lg');
    },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent the default form submission immediately

    $(e.target).find('.summernote, .summernote-simple').each(function() {
      try { $(this).val($(this).summernote('code')); } catch(ex) {}
    });
    let form_data = $(e.target).serialize();

    $.ajax({
      type: 'POST',
      url: '/change/document/status',
      data: form_data,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');

          // Reload the page after a short delay to allow the message to be visible
          setTimeout(function() {
            location.reload();
          }, 2000); // Delay for 2 seconds
        } else if (response.status === 'error') {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        }
      },
      error: function(xhr) {
        let response = JSON.parse(xhr.responseText);
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
      },
      complete: function() {
        form.stopProgress(); // Stop the loading spinner
      }
    });
  },
  buttons: [
    {
      text: 'Close',
      submit: true,
      class: 'btn btn-dark btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-completed").fireModal({
  title: 'Complete',
  body: $("#modal-completed-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
        $(modal).find('.modal-dialog').addClass('modal-lg');
    },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent the default form submission immediately

    $(e.target).find('.summernote, .summernote-simple').each(function() {
      try { $(this).val($(this).summernote('code')); } catch(ex) {}
    });
    let form_data = $(e.target).serialize();

    $.ajax({
      type: 'POST',
      url: '/change/document/status',
      data: form_data,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');

          // Reload the page after a short delay to allow the message to be visible
          setTimeout(function() {
            location.reload();
          }, 2000); // Delay for 2 seconds
        } else if (response.status === 'error') {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        }
      },
      error: function(xhr) {
        let response = JSON.parse(xhr.responseText);
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
      },
      complete: function() {
        form.stopProgress(); // Stop the loading spinner
      }
    });
  },
  buttons: [
    {
      text: 'Complete',
      submit: true,
      class: 'btn btn-dark btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-pay").fireModal({
  title: 'Payment Details',
  body: $("#modal-pay-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
        $(modal).find('.modal-dialog').addClass('modal-lg');
    },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent the default form submission immediately

    $(e.target).find('.summernote, .summernote-simple').each(function() {
      try { $(this).val($(this).summernote('code')); } catch(ex) {}
    });
    let form_data = $(e.target).serialize();

    $.ajax({
      type: 'POST',
      url: '/change/document/status',
      data: form_data,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');

          // Reload the page after a short delay to allow the message to be visible
          setTimeout(function() {
            location.reload();
          }, 2000); // Delay for 2 seconds
        } else if (response.status === 'error') {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        }
      },
      error: function(xhr) {
        let response = JSON.parse(xhr.responseText);
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
      },
      complete: function() {
        form.stopProgress(); // Stop the loading spinner
      }
    });
  },
  buttons: [
    {
      text: 'Update',
      submit: true,
      class: 'btn btn-primary btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-process-payment").fireModal({
  title: 'Process Payment',
  body: $("#modal-process-payment-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent the default form submission immediately

    $(e.target).find('.summernote, .summernote-simple').each(function() {
      try { $(this).val($(this).summernote('code')); } catch(ex) {}
    });
    let form_data = $(e.target).serialize();

    $.ajax({
      type: 'POST',
      url: '/change/document/status',
      data: form_data,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');

          // Reload the page after a short delay to allow the message to be visible
          setTimeout(function() {
            location.reload();
          }, 2000); // Delay for 2 seconds
        } else if (response.status === 'error') {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        }
      },
      error: function(xhr) {
        let response = JSON.parse(xhr.responseText);
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
      },
      complete: function() {
        form.stopProgress(); // Stop the loading spinner
      }
    });
  },
  buttons: [
    {
      text: 'Assign',
      submit: true,
      class: 'btn btn-primary btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-reject").fireModal({
  title: 'Reject',
  body: $("#modal-reject-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
        $(modal).find('.modal-dialog').addClass('modal-lg');
    },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent the default form submission immediately

    $(e.target).find('.summernote, .summernote-simple').each(function() {
      try { $(this).val($(this).summernote('code')); } catch(ex) {}
    });
    let form_data = $(e.target).serialize();

    $.ajax({
      type: 'POST',
      url: '/change/document/status',
      data: form_data,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');

          // Reload the page after a short delay to allow the message to be visible
          setTimeout(function() {
            location.reload();
          }, 2000); // Delay for 2 seconds
        } else if (response.status === 'error') {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        }
      },
      error: function(xhr) {
        let response = JSON.parse(xhr.responseText);
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
      },
      complete: function() {
        form.stopProgress(); // Stop the loading spinner
      }
    });
  },
  buttons: [
    {
      text: 'Reject',
      submit: true,
      class: 'btn btn-danger btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-delete").fireModal({
  title: 'Delete',
  body: $("#modal-delete-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
        $(modal).find('.modal-dialog').addClass('modal-lg');
    },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent the default form submission immediately

    $(e.target).find('.summernote, .summernote-simple').each(function() {
      try { $(this).val($(this).summernote('code')); } catch(ex) {}
    });
    let form_data = $(e.target).serialize();

    $.ajax({
      type: 'POST',
      url: '/delete/document/',
      data: form_data,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');

          // Reload the page after a short delay to allow the message to be visible
          setTimeout(function() {
              if (response.redirect) {
                  window.location.href = response.redirect;  // ✅ Redirect here
              } else {
                  location.reload(); // fallback
              }
          }, 1500); // Delay for 2 seconds
        } else if (response.status === 'error') {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        }
      },
      error: function(xhr) {
        let response = JSON.parse(xhr.responseText);
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
      },
      complete: function() {
        form.stopProgress(); // Stop the loading spinner
      }
    });
  },
  buttons: [
    {
      text: 'Delete',
      submit: true,
      class: 'btn btn-danger btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-revoke").fireModal({
  title: 'Revoke',
  body: $("#modal-revoke-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
        $(modal).find('.modal-dialog').addClass('modal-lg');
    },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent the default form submission immediately

    $(e.target).find('.summernote, .summernote-simple').each(function() {
      try { $(this).val($(this).summernote('code')); } catch(ex) {}
    });
    let form_data = $(e.target).serialize();

    $.ajax({
      type: 'POST',
      url: '/change/document/status',
      data: form_data,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');

          // Reload the page after a short delay to allow the message to be visible
          setTimeout(function() {
            location.reload();
          }, 2000); // Delay for 2 seconds
        } else if (response.status === 'error') {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        }
      },
      error: function(xhr) {
        let response = JSON.parse(xhr.responseText);
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
      },
      complete: function() {
        form.stopProgress(); // Stop the loading spinner
      }
    });
  },
  buttons: [
    {
      text: 'Revoke',
      submit: true,
      class: 'btn btn-outline-danger btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-pending").fireModal({
  title: 'Pending',
  body: $("#modal-pending-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
        $(modal).find('.modal-dialog').addClass('modal-lg');
    },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent the default form submission immediately

    $(e.target).find('.summernote, .summernote-simple').each(function() {
      try { $(this).val($(this).summernote('code')); } catch(ex) {}
    });
    let form_data = $(e.target).serialize();

    $.ajax({
      type: 'POST',
      url: '/change/document/status',
      data: form_data,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');

          // Reload the page after a short delay to allow the message to be visible
          setTimeout(function() {
            location.reload();
          }, 2000); // Delay for 2 seconds
        } else if (response.status === 'error') {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        }
      },
      error: function(xhr) {
        let response = JSON.parse(xhr.responseText);
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
      },
      complete: function() {
        form.stopProgress(); // Stop the loading spinner
      }
    });
  },
  buttons: [
    {
      text: 'Pending',
      submit: true,
      class: 'btn btn-warning btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-discuss").fireModal({
  title: 'Discuss',
  body: $("#modal-discuss-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
        $(modal).find('.modal-dialog').addClass('modal-lg');
    },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent the default form submission immediately

    $(e.target).find('.summernote, .summernote-simple').each(function() {
      try { $(this).val($(this).summernote('code')); } catch(ex) {}
    });
    let form_data = $(e.target).serialize();

    $.ajax({
      type: 'POST',
      url: '/change/document/status',
      data: form_data,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');

          // Reload the page after a short delay to allow the message to be visible
          setTimeout(function() {
            location.reload();
          }, 2000); // Delay for 2 seconds
        } else if (response.status === 'error') {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        }
      },
      error: function(xhr) {
        let response = JSON.parse(xhr.responseText);
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
      },
      complete: function() {
        form.stopProgress(); // Stop the loading spinner
      }
    });
  },
  buttons: [
    {
      text: 'Discuss',
      submit: true,
      class: 'btn btn-light btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-noted").fireModal({
  title: 'Noted',
  body: $("#modal-noted-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
        $(modal).find('.modal-dialog').addClass('modal-lg');
    },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent the default form submission immediately

    $(e.target).find('.summernote, .summernote-simple').each(function() {
      try { $(this).val($(this).summernote('code')); } catch(ex) {}
    });
    let form_data = $(e.target).serialize();

    $.ajax({
      type: 'POST',
      url: '/change/document/status',
      data: form_data,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');

          // Reload the page after a short delay to allow the message to be visible
          setTimeout(function() {
            location.reload();
          }, 2000); // Delay for 2 seconds
        } else if (response.status === 'error') {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        }
      },
      error: function(xhr) {
        let response = JSON.parse(xhr.responseText);
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
      },
      complete: function() {
        form.stopProgress(); // Stop the loading spinner
      }
    });
  },
  buttons: [
    {
      text: 'Noted',
      submit: true,
      class: 'btn btn-info btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-forward-doc").fireModal({
  title: 'Forward',
  body: $("#modal-forward-doc-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
        $(modal).find('.modal-dialog').addClass('modal-lg');
    },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent the default form submission immediately

    $(e.target).find('.summernote, .summernote-simple').each(function() {
      try { $(this).val($(this).summernote('code')); } catch(ex) {}
    });
    let form_data = $(e.target).serialize();

    $.ajax({
      type: 'POST',
      url: '/change/document/status',
      data: form_data,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');

          // Reload the page after a short delay to allow the message to be visible
          setTimeout(function() {
            location.reload();
          }, 2000); // Delay for 2 seconds
        } else if (response.status === 'error') {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        }
      },
      error: function(xhr) {
        let response = JSON.parse(xhr.responseText);
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
      },
      complete: function() {
        form.stopProgress(); // Stop the loading spinner
      }
    });
  },
  buttons: [
    {
      text: 'Forward',
      submit: true,
      class: 'btn btn-outline-dark btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-re-submit").fireModal({
  title: 'Re-Submit',
  body: $("#modal-re-submit-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
        $(modal).find('.modal-dialog').addClass('modal-lg');
    },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent the default form submission immediately

    $(e.target).find('.summernote, .summernote-simple').each(function() {
      try { $(this).val($(this).summernote('code')); } catch(ex) {}
    });
    let form_data = $(e.target).serialize();

    $.ajax({
      type: 'POST',
      url: '/change/document/status',
      data: form_data,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');

          // Reload the page after a short delay to allow the message to be visible
          setTimeout(function() {
            location.reload();
          }, 2000); // Delay for 2 seconds
        } else if (response.status === 'error') {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        }
      },
      error: function(xhr) {
        let response = JSON.parse(xhr.responseText);
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
      },
      complete: function() {
        form.stopProgress(); // Stop the loading spinner
      }
    });
  },
  buttons: [
    {
      text: 'Re-Submit',
      submit: true,
      class: 'btn btn-outline-danger btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-comment").fireModal({
  title: 'Comment',
  body: $("#modal-comment-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
        $(modal).find('.modal-dialog').addClass('modal-lg');
    },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent default form submission

    let formData = new FormData(e.target);

    $.ajax({
        type: 'POST',
        url: '/change/document/status',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response.status === 'success') {
                modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');
                setTimeout(function() {
                    location.reload();
                }, 2000);
            } else if (response.status === 'error') {
                modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
            }
        },
        error: function(xhr) {
            let response = JSON.parse(xhr.responseText);
            modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        },
        complete: function() {
            form.stopProgress(); // Stop the loading spinner
        }
    });
  },
  buttons: [
    {
      text: 'Comment',
      submit: true,
      class: 'btn btn-outline-info btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-request-doc").fireModal({
  title: 'Request',
  body: $("#modal-request-doc-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
        $(modal).find('.modal-dialog').addClass('modal-lg');
    },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent default form submission

    let formData = new FormData(e.target);

    $.ajax({
        type: 'POST',
        url: '/change/document/status',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response.status === 'success') {
                modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');
                setTimeout(function() {
                    location.reload();
                }, 2000);
            } else if (response.status === 'error') {
                modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
            }
        },
        error: function(xhr) {
            let response = JSON.parse(xhr.responseText);
            modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        },
        complete: function() {
            form.stopProgress(); // Stop the loading spinner
        }
    });
  },
  buttons: [
    {
      text: 'Request',
      submit: true,
      class: 'btn btn-outline-dark btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-post-close").fireModal({
  title: 'Status',
  body: $("#modal-post-close-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
        $(modal).find('.modal-dialog').addClass('modal-lg');
    },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent default form submission

    let formData = new FormData(e.target);

    $.ajax({
        type: 'POST',
        url: '/close/postal/status',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response.status === 'success') {
                modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');
                setTimeout(function() {
                    location.reload();
                }, 2000);
            } else if (response.status === 'error') {
                modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
            }
        },
        error: function(xhr) {
            let response = JSON.parse(xhr.responseText);
            modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        },
        complete: function() {
            form.stopProgress(); // Stop the loading spinner
        }
    });
  },
  buttons: [
    {
      text: 'Update',
      submit: true,
      class: 'btn btn-warning btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-cancel-request").fireModal({
  title: 'Request',
  body: $("#modal-cancel-request-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
        $(modal).find('.modal-dialog').addClass('modal-lg');
    },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent default form submission

    let formData = new FormData(e.target);

    $.ajax({
        type: 'POST',
        url: '/change/document/status',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response.status === 'success') {
                modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');
                setTimeout(function() {
                    location.reload();
                }, 2000);
            } else if (response.status === 'error') {
                modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
            }
        },
        error: function(xhr) {
            let response = JSON.parse(xhr.responseText);
            modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        },
        complete: function() {
            form.stopProgress(); // Stop the loading spinner
        }
    });
  },
  buttons: [
    {
      text: 'Cancel Doc Request',
      submit: true,
      class: 'btn btn-outline-danger btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-send-doc").fireModal({
  title: 'Request',
  body: $("#modal-send-doc-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
        $(modal).find('.modal-dialog').addClass('modal-lg');
    },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent default form submission

    let formData = new FormData(e.target);

    $.ajax({
        type: 'POST',
        url: '/change/document/status',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response.status === 'success') {
                modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');
                setTimeout(function() {
                    location.reload();
                }, 2000);
            } else if (response.status === 'error') {
                modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
            }
        },
        error: function(xhr) {
            let response = JSON.parse(xhr.responseText);
            modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        },
        complete: function() {
            form.stopProgress(); // Stop the loading spinner
        }
    });
  },
  buttons: [
    {
      text: 'Send',
      submit: true,
      class: 'btn btn-outline-success btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-upload").fireModal({
  title: 'Upload',
  body: $("#modal-upload-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent default form submission

    let formData = new FormData(e.target);

    $.ajax({
        type: 'POST',
        url: '/upload/post',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response.status === 'success') {
                modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');
                setTimeout(function() {
                    location.reload();
                }, 2000);
            } else if (response.status === 'error') {
                modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
            }
        },
        error: function(xhr) {
            let response = JSON.parse(xhr.responseText);
            modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        },
        complete: function() {
            form.stopProgress(); // Stop the loading spinner
        }
    });
  },
  buttons: [
    {
      text: 'Upload',
      submit: true,
      class: 'btn btn-info btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-categorize").fireModal({
  title: 'Categorize & File',
  body: $("#modal-categorize-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  onFormSubmit: function(modal, e, form) {
    e.preventDefault(); // Prevent the default form submission immediately

    $(e.target).find('.summernote, .summernote-simple').each(function() {
      try { $(this).val($(this).summernote('code')); } catch(ex) {}
    });
    let form_data = $(e.target).serialize();

    $.ajax({
      type: 'POST',
      url: '/categorize/post/file',
      data: form_data,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');

          // Reload the page after a short delay to allow the message to be visible
          setTimeout(function() {
            location.reload();
          }, 2000); // Delay for 2 seconds
        } else if (response.status === 'error') {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
        }
      },
      error: function(xhr) {
        let response = JSON.parse(xhr.responseText);
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
      },
      complete: function() {
        form.stopProgress(); // Stop the loading spinner
      }
    });
  },
  buttons: [
    {
      text: 'Categorize & File',
      submit: true,
      class: 'btn btn-dark btn-shadow',
      handler: function(modal) {
        // No additional action needed here
      }
    }
  ]
});

$("#modal-select-finance-head").fireModal({
  title: 'Select Finance Head',
  body: $("#modal-select-finance-head-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
    $(modal).find('.modal-dialog').addClass('modal-md');
  },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault();

    let submitBtn = modal.find('.btn-primary');
    submitBtn.prop('disabled', true);

    $(e.target).find('.summernote, .summernote-simple').each(function() {
      try { $(this).val($(this).summernote('code')); } catch(ex) {}
    });
    let form_data = $(e.target).serialize();

    $.ajax({
      type: 'POST',
      url: '/change/document/status',
      data: form_data,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');
          setTimeout(function() { location.reload(); }, 2000);
        } else {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
          submitBtn.prop('disabled', false);
        }
      },
      error: function(xhr) {
        let msg = 'An error occurred.';
        try { msg = JSON.parse(xhr.responseText).message; } catch(ex) {}
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + msg + '</div>');
        submitBtn.prop('disabled', false);
      },
      complete: function() { form.stopProgress(); }
    });
  },
  buttons: [
    {
      text: 'Confirm Selection',
      submit: true,
      class: 'btn btn-info btn-shadow',
      handler: function(modal) {}
    }
  ]
});

$("#modal-purchase-order").fireModal({
  title: 'Create Purchase Order',
  body: $("#modal-purchase-order-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
    $(modal).find('.modal-dialog').addClass('modal-lg');
  },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault();

    let submitBtn = modal.find('.btn-primary');
    submitBtn.prop('disabled', true);

    let formData = new FormData(e.target);

    $.ajax({
      type: 'POST',
      url: '/change/document/status',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');
          setTimeout(function() { location.reload(); }, 2000);
        } else {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
          submitBtn.prop('disabled', false);
        }
      },
      error: function(xhr) {
        let msg = 'An error occurred.';
        try { msg = JSON.parse(xhr.responseText).message; } catch(ex) {}
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + msg + '</div>');
        submitBtn.prop('disabled', false);
      },
      complete: function() { form.stopProgress(); }
    });
  },
  buttons: [
    {
      text: 'Upload Purchase Order',
      submit: true,
      class: 'btn btn-primary btn-shadow',
      handler: function(modal) {}
    }
  ]
});

$("#modal-work-order").fireModal({
  title: 'Create Work Order',
  body: $("#modal-work-order-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
    $(modal).find('.modal-dialog').addClass('modal-lg');
  },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault();

    let submitBtn = modal.find('.btn-primary');
    submitBtn.prop('disabled', true);

    let formData = new FormData(e.target);

    $.ajax({
      type: 'POST',
      url: '/change/document/status',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');
          setTimeout(function() { location.reload(); }, 2000);
        } else {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
          submitBtn.prop('disabled', false);
        }
      },
      error: function(xhr) {
        let msg = 'An error occurred.';
        try { msg = JSON.parse(xhr.responseText).message; } catch(ex) {}
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + msg + '</div>');
        submitBtn.prop('disabled', false);
      },
      complete: function() { form.stopProgress(); }
    });
  },
  buttons: [
    {
      text: 'Upload Work Order',
      submit: true,
      class: 'btn btn-primary btn-shadow',
      handler: function(modal) {}
    }
  ]
});

$("#modal-sanction").fireModal({
  title: 'Sanction Amount',
  body: $("#modal-sanction-part"),
  footerClass: 'bg-whitesmoke',
  autoFocus: false,
  created: function(modal) {
    $(modal).find('.modal-dialog').addClass('modal-lg');
  },
  onFormSubmit: function(modal, e, form) {
    e.preventDefault();

    let submitBtn = modal.find('.btn-primary');
    submitBtn.prop('disabled', true);

    $(e.target).find('.summernote, .summernote-simple').each(function() {
      try { $(this).val($(this).summernote('code')); } catch(ex) {}
    });
    let form_data = $(e.target).serialize();

    $.ajax({
      type: 'POST',
      url: '/change/document/status',
      data: form_data,
      success: function(response) {
        if (response.status === 'success') {
          modal.find('.modal-body').prepend('<div class="alert alert-success">' + response.message + '</div>');
          setTimeout(function() { location.reload(); }, 2000);
        } else {
          modal.find('.modal-body').prepend('<div class="alert alert-danger">' + response.message + '</div>');
          submitBtn.prop('disabled', false);
        }
      },
      error: function(xhr) {
        let msg = 'An error occurred.';
        try { msg = JSON.parse(xhr.responseText).message; } catch(ex) {}
        modal.find('.modal-body').prepend('<div class="alert alert-danger">' + msg + '</div>');
        submitBtn.prop('disabled', false);
      },
      complete: function() { form.stopProgress(); }
    });
  },
  buttons: [
    {
      text: 'Confirm Sanction',
      submit: true,
      class: 'btn btn-primary btn-shadow',
      handler: function(modal) {}
    }
  ]
});