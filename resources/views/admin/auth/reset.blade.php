@extends('layouts.admin')
@section('content')
    <script>
        @if(Session::has('message'))
        $(function () {
            $.Huimodalalert('{{ Session::get('message') }}', 2000);
                    @if(Session::get('code') == 2)
            var index = parent.layer.getFrameIndex(window.name);
            setTimeout(function(){parent.layer.close(index)}, 2000);
            @endif
        });
        @endif
    </script>
    <div class="container">
        <div class="container mt-50 col-sm-5 f-clean">
            <div class="row">
                <div>
                    <div class="panel panel-default">
                        <div class="panel-header">Reset Password</div>

                        <div class="panel-body">
                            <form class="form form-horizontal" method="POST" action="{{ url('admin/password/changePassword') }}">
                                {{ csrf_field() }}
                                <input type="hidden" name="userId" value="{{ Auth::guard('admin')->user()->id }}">
                                <div class="row cl{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="password" class="form-label col-xs-5 col-sm-5">Password</label>
                                    <div class="formControls col-xs-5 col-sm-5">
                                        <input id="password" type="password" class="input-text radius" name="password" value="{{ old('password') }}" required autofocus>
                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="row cl{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="password_confirmation" class="form-label col-xs-5 col-sm-5">Confirm Password</label>

                                    <div class="formControls col-xs-5 col-sm-5">
                                        <input id="password_confirmation" type="password" class="input-text radius" name="password_confirmation" required>

                                        @if ($errors->has('password_confirmation'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="row cl">
                                    <div class="text-c">
                                        <button type="submit" class="btn btn-primary radius">
                                            Reset Password
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
