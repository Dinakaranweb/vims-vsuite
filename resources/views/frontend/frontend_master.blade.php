<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>VIMS-Suite &mdash; VMRF</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="{{ asset('assets/modules/jqvmap/dist/jqvmap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/weather-icon/css/weather-icons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/weather-icon/css/weather-icons-wind.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/summernote/summernote-bs4.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/chocolat/dist/css/chocolat.css') }}">

  <!-- Data Tables CSS -->
  <link rel="stylesheet" href="{{ asset('assets/modules/datatables/datatables.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css') }}">

  <!-- Registration CSS -->
  <link rel="stylesheet" href="{{ asset('assets/modules/jquery-selectric/selectric.css') }}">

  <!-- Editor CSS -->
  <link rel="stylesheet" href="{{ asset('assets/modules/codemirror/lib/codemirror.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/codemirror/theme/duotone-dark.css') }}">

  <!-- Date Picker -->
  <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap-daterangepicker/daterangepicker.css') }}">

  <!-- Multiple Upload -->
  <link rel="stylesheet" href="{{ asset('assets/modules/dropzonejs/dropzone.css') }}">

  <!-- Toastr CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet">

  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
  <!-- Custom overrides — must load AFTER style.css and components.css -->
  <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

  <!-- Modal CSS -->
  <link rel="stylesheet" href="{{ asset('assets/modules/prism/prism.css') }}">

  <link rel="icon" type="image/png" href="{{ asset('assets/img/vm/icon.jpg') }}">

  <!-- Hide sidebar immediately on mobile before JS runs (prevents flash) -->
  <script>if(window.innerWidth<=1024){document.body.classList.add('sidebar-gone');}</script>

<!-- Start GA -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-94034622-3');
</script>

<!-- PDF.js CSS and JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';
</script>


<style>

  .section-lead{
    font-weight: normal;
  }

  .dataTables_info{
    font-weight: normal;
  }

  .breadcrumb-item{
    font-weight: normal;
  }

  .footer-left{
    color: #000;
    font-weight: normal;
  }

  .selectgroup-button{
    font-weight: normal;
  }

  .selectric-items{
    font-weight: normal;
  }

  .table {
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;
    border-collapse: collapse;
    background-color: white;
}

/* Add borders to table */
.table th,
.table td {
    border: 1px solid #c0c0c0 !important;
    vertical-align: top;
    text-align: center;
}

/* Ensure headers have bold font */
.table th {
    font-weight: 600 !important;
    color: #000 !important;
    background-color: #f3f3f3;
}

.card-body td{
  font-weight: normal;
}

/* Ensure table data cells have normal font weight */
.table td {
    font-weight: normal !important;
}

/* Ensure all rows have a white background */
.table tbody tr {
    background-color: white;
}

/* Remove striping for table rows */
.table-striped tbody tr:nth-of-type(odd),
.table-striped tbody tr:nth-of-type(even) {
    background-color: white;
}

/* Add hover effect */
.table tbody tr:hover {
    background-color: #f8f8f8;
}

.note-editor.note-frame{
  border:1px solid #6fb4df;
}

.note-toolbar-wrapper{
  border-bottom: 1px solid #6fb4df;
}
    
.rating {
  display: flex;
  width: 100%;
  justify-content: center;
  overflow: hidden;
  flex-direction: row-reverse;
  height: 150px;
  position: relative;
}

.rating-0 {
  filter: grayscale(100%);
}

.rating > input {
  display: none;
}

.rating > label {
  cursor: pointer;
  width: 40px;
  height: 40px;
  margin-top: auto;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='126.729' height='126.73'%3e%3cpath fill='%23e3e3e3' d='M121.215 44.212l-34.899-3.3c-2.2-.2-4.101-1.6-5-3.7l-12.5-30.3c-2-5-9.101-5-11.101 0l-12.4 30.3c-.8 2.1-2.8 3.5-5 3.7l-34.9 3.3c-5.2.5-7.3 7-3.4 10.5l26.3 23.1c1.7 1.5 2.4 3.7 1.9 5.9l-7.9 32.399c-1.2 5.101 4.3 9.3 8.9 6.601l29.1-17.101c1.9-1.1 4.2-1.1 6.1 0l29.101 17.101c4.6 2.699 10.1-1.4 8.899-6.601l-7.8-32.399c-.5-2.2.2-4.4 1.9-5.9l26.3-23.1c3.8-3.5 1.6-10-3.6-10.5z'/%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: center;
  background-size: 76%;
  transition: 0.3s;
}

.rating > input:checked ~ label,
.rating > input:checked ~ label ~ label {
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='126.729' height='126.73'%3e%3cpath fill='%23fcd93a' d='M121.215 44.212l-34.899-3.3c-2.2-.2-4.101-1.6-5-3.7l-12.5-30.3c-2-5-9.101-5-11.101 0l-12.4 30.3c-.8 2.1-2.8 3.5-5 3.7l-34.9 3.3c-5.2.5-7.3 7-3.4 10.5l26.3 23.1c1.7 1.5 2.4 3.7 1.9 5.9l-7.9 32.399c-1.2 5.101 4.3 9.3 8.9 6.601l29.1-17.101c1.9-1.1 4.2-1.1 6.1 0l29.101 17.101c4.6 2.699 10.1-1.4 8.899-6.601l-7.8-32.399c-.5-2.2.2-4.4 1.9-5.9l26.3-23.1c3.8-3.5 1.6-10-3.6-10.5z'/%3e%3c/svg%3e");
}

.rating > input:not(:checked) ~ label:hover,
.rating > input:not(:checked) ~ label:hover ~ label {
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='126.729' height='126.73'%3e%3cpath fill='%23d8b11e' d='M121.215 44.212l-34.899-3.3c-2.2-.2-4.101-1.6-5-3.7l-12.5-30.3c-2-5-9.101-5-11.101 0l-12.4 30.3c-.8 2.1-2.8 3.5-5 3.7l-34.9 3.3c-5.2.5-7.3 7-3.4 10.5l26.3 23.1c1.7 1.5 2.4 3.7 1.9 5.9l-7.9 32.399c-1.2 5.101 4.3 9.3 8.9 6.601l29.1-17.101c1.9-1.1 4.2-1.1 6.1 0l29.101 17.101c4.6 2.699 10.1-1.4 8.899-6.601l-7.8-32.399c-.5-2.2.2-4.4 1.9-5.9l26.3-23.1c3.8-3.5 1.6-10-3.6-10.5z'/%3e%3c/svg%3e");
}

.emoji-wrapper {
  width: 100%;
  text-align: center;
  height: 100px;
  overflow: hidden;
  position: absolute;
  top: 0;
  left: 0;
}

.emoji-wrapper:before,
.emoji-wrapper:after {
  content: "";
  height: 15px;
  width: 100%;
  position: absolute;
  left: 0;
  z-index: 1;
}

.emoji-wrapper:before {
  top: 0;
  background: linear-gradient(to bottom, white 0%, white 35%, rgba(255, 255, 255, 0) 100%);
}

.emoji-wrapper:after {
  bottom: 0;
  background: linear-gradient(to top, white 0%, white 35%, rgba(255, 255, 255, 0) 100%);
}

.emoji {
  display: flex;
  flex-direction: column;
  align-items: center;
  transition: 0.3s;
}

.emoji > svg {
  margin: 15px 0;
  width: 70px;
  height: 70px;
  flex-shrink: 0;
}

#rating-1:checked ~ .emoji-wrapper > .emoji {
  transform: translateY(-100px);
}

#rating-2:checked ~ .emoji-wrapper > .emoji {
  transform: translateY(-200px);
}

#rating-3:checked ~ .emoji-wrapper > .emoji {
  transform: translateY(-300px);
}

#rating-4:checked ~ .emoji-wrapper > .emoji {
  transform: translateY(-400px);
}

#rating-5:checked ~ .emoji-wrapper > .emoji {
  transform: translateY(-500px);
}

.feedback {
  margin: 0 auto;
  max-width: 360px;
  background-color: #fff;
  width: 100%;
  padding: 30px;
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  flex-wrap: wrap;
  align-items: center;
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
}
</style>
<!-- /END GA --></head>

<body>

    @yield('content')

  <script src="{{ asset('assets/modules/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/modules/popper.js') }}"></script>
  <script src="{{ asset('assets/modules/tooltip.js') }}"></script>
  <script src="{{ asset('assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
  <script src="{{ asset('assets/modules/moment.min.js') }}"></script>
  <script src="{{ asset('assets/js/stisla.js') }}"></script>
  
  <!-- JS Libraies -->
  <script src="{{ asset('assets/modules/simple-weather/jquery.simpleWeather.min.js') }}"></script>
  <script src="{{ asset('assets/modules/chart.min.js') }}"></script>
  <script src="{{ asset('assets/modules/jqvmap/dist/jquery.vmap.min.js') }}"></script>
  <script src="{{ asset('assets/modules/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
  <script src="{{ asset('assets/modules/summernote/summernote-bs4.js') }}"></script>
  <script src="{{ asset('assets/modules/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>

  <!-- Page Specific JS File -->
  <script src="{{ asset('assets/js/page/index-0.js') }}"></script>

  <!-- DataTables JS -->
  <script src="{{ asset('assets/modules/datatables/datatables.min.js') }}"></script>
  <script src="{{ asset('assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
  <script src="{{ asset('assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js') }}"></script>
  <script src="{{ asset('assets/modules/jquery-ui/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('assets/js/page/modules-datatables.js') }}"></script>

  <!-- Sweet Alert JS -->
  <script src="{{ asset('assets/modules/sweetalert/sweetalert.min.js') }}"></script>
  <script src="{{ asset('assets/js/page/modules-sweetalert.js') }}"></script>

  <!-- Chart JS v4 -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
  <script src="{{ asset('assets/js/page/modules-chartjs.js') }}"></script>

  @stack('scripts')

  <!-- Multiple Upload -->
  <script src="{{ asset('assets/js/page/components-multiple-upload.js') }}"></script>

  <!-- Toastr JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

  <!-- Registration JS -->
  <script src="{{ asset('assets/modules/jquery-pwstrength/jquery.pwstrength.min.js') }}"></script>
  <script src="{{ asset('assets/modules/jquery-selectric/jquery.selectric.min.js') }}"></script>
  <script src="{{ asset('assets/js/page/auth-register.js') }}"></script>

  <!-- Editor JS -->
  <script src="{{ asset('assets/modules/codemirror/lib/codemirror.js') }}"></script>
  <script src="{{ asset('assets/modules/codemirror/mode/javascript/javascript.js') }}"></script>

  <!-- Date Picker -->
  <script src="{{ asset('assets/modules/cleave-js/dist/cleave.min.js') }}"></script>
  <script src="{{ asset('assets/modules/cleave-js/dist/addons/cleave-phone.us.js') }}"></script>
  <script src="{{ asset('assets/modules/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
  <script src="{{ asset('assets/js/page/forms-advanced-forms.js') }}"></script>
  
  <!-- Template JS File -->
  <script src="{{ asset('assets/js/scripts.js') }}"></script>
  <script src="{{ asset('assets/js/custom.js') }}"></script>

  <!-- Modal JS -->
  <script src="{{ asset('assets/modules/prism/prism.js') }}"></script>
  <script src="{{ asset('assets/js/page/bootstrap-modal.js') }}"></script>

  <script>
    @if(Session::has('message'))
        var type = "{{ Session::get('alert-type', 'info') }}";
        switch(type){
            case 'info':
                toastr.info("{{ Session::get('message') }}", '', {
                    progressBar: true,
                    closeButton: true
                });
                break;
            case 'warning':
                toastr.warning("{{ Session::get('message') }}", '', {
                    progressBar: true,
                    closeButton: true
                });
                break;
            case 'success':
                toastr.success("{{ Session::get('message') }}", '', {
                    progressBar: true,
                    closeButton: true
                });
                break;
            case 'error':
                toastr.error("{{ Session::get('message') }}", '', {
                    progressBar: true,
                    closeButton: true
                });
                break;
        }
    @endif

  </script>

</body>
</html>