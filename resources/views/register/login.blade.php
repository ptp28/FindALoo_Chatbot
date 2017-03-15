@extends('register.layouts.master')

@section('title', 'e-Yantra Login')

@section('content')
		<div class="section white">
			<div class="row container">
				<div class = "row">
					<div class="col s12">
						@if (session('success'))
							<div class="card-panel teal lighten-2 white-text">{{session('success')}}</div>
						@endif

						@if (session('error'))
							<div class="card-panel red white-text">{{session('error')}}</div>
						@endif
					</div>
				</div>
				<div class="row">
					<div class="col s12 l6">
					@if(isset($notice))
						@foreach($notice as $row)
							<?php
								$today = (new DateTime())->format("Y-m-d");
								$start = (new DateTime($row->start_date))->format("Y-m-d");
								$end = (new DateTime($row->end_date))->format('Y-m-d');
							?>
							@if(($start <= $today) && ($today <= $end))
								<div class="card-panel blue lighten-2">
									{!!$row->notice_text!!}
								</div>
							@endif
						@endforeach
					@endif
					</div>
					<div class="col s12 l6">
						<div class="card-panel teal lighten-2 white-text">Sign In</div>
						<form method="POST" action="{!!route('user_login')!!}">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<div class="row">
								<div class="input-field col s12">
									<input type="email" class="validate" id="email" name="email" value="{!!old('email')!!}">
									<label for="email" class="">Username/Email</label>
									@if($errors->loginForm->has('email'))
										<span class="red-text">{!! $errors->loginForm->first('email') !!}</span>
									@endif
								</div>
							</div>
							<div class="row">
								<div class="input-field col s12">
									<input type="password" class="validate" id="password" name="password">
									<label for="password" class="">Password</label>
									@if($errors->loginForm->has('password'))
										<span class="red-text">{!! $errors->loginForm->first('password') !!}</span>
									@endif
								</div>
							</div>
							<div class="row">
								<div class="col s6">
									<a href="{!!route('forgotpass')!!}" name="action" class="btn waves-effect waves-light btn-large">Reset Password
									 		<i class="material-icons right">send</i>
									</a>
								</div>
								<div class="col s6">
									<button name="action" type="submit" class="btn waves-effect waves-light btn-large right">Login
									 		<i class="material-icons right">send</i>
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div class="parallax-container">
			<div class="parallax"><img src="{!! asset('img/register/group_photo.jpg')!!}"></div>
		</div>
@endsection

@section('readyscript')
    $(document).ready(function(){
      $('.parallax').parallax();
    });
@endsection