<!DOCTYPE html>
<html  lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8" />
	<title>{{ __('keywords.Change Password')}} Admin</title>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="description" content="" />
	<meta name="author" content="" />
	
	<link href="{{url('assets/theme_assets/css/app.min.css')}}" rel="stylesheet" />

	<style>
        .headerTitle {
            font-size: 25px;
            font-weight: 700;
            margin-top: 2rem;
        }

        .for-container {
            width: 91%;
            border: 1px solid #D8D8D8;
            margin-top: 3%;
            margin-bottom: 3%;
        }

        .for-padding {
            padding: 3%;
        }
    </style>
</head>
<body>

	    <div class="container for-container rtl" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
        <h2 class="text-center mt-3 headerTitle">{{ __('keywords.Privacy Policy')}}</h2>
        <div class="for-padding">
              {!! $privacy_policy->description !!}
        
        </div>
    </div>
    
	

	<script src="{{url('assets/theme_assets/js/app.min.js')}}"></script>
	   <script src="{{asset('/')}}vendor/ckeditor/ckeditor/ckeditor.js"></script>
    <script src="{{asset('/')}}vendor/ckeditor/ckeditor/adapters/jquery.js"></script>
    <script>
        $('#editor').ckeditor({
            contentsLangDirection : '{{Session::get('direction')}}',
        });
    </script>
</body>
</html>

