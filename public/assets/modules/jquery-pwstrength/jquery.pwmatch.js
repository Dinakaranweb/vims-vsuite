const passwordConfirm = document.querySelector('#password2');
      passwordConfirm.addEventListener('input', function() {
        const password = document.querySelector('#password').value;
        const passwordConfirmValue = this.value;

        if (password !== passwordConfirmValue) {
          // Show an error message
          const errorMessage = this.nextElementSibling;
          errorMessage.textContent = 'Passwords do not match.';
          errorMessage.style.display = 'block';

          // Prevent form submission
          form.onsubmit = function(event) {
            event.preventDefault();
          };
        } else {
          // Hide the error message
          const errorMessage = this.nextElementSibling;
          errorMessage.textContent = '';
          errorMessage.style.display = 'none';

          // Allow form submission
          form.onsubmit = function() {
            return true;
          };
        }
      });

      const form = document.querySelector('form');
      form.addEventListener('submit', function(event) {
        event.preventDefault();

        const password = document.querySelector('#password').value;
        const passwordConfirm = document.querySelector('#password2').value;

        if (password !== passwordConfirm) {
          // Show an error message
          const errorMessage = document.querySelector('#password2 + .invalid-feedback');
          errorMessage.textContent = 'Passwords do not match.';
          errorMessage.style.display = 'block';

          // Prevent form submission
          return false;
        } else {
          // Hide the error message
          const errorMessage = document.querySelector('#password2 + .invalid-feedback');
          errorMessage.textContent = '';
          errorMessage.style.display = 'none';
        }

        // Submit the form
        form.submit();
      });