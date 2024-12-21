@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Attendance Form</div>

                <div class="card-body">
                    @if(isset($error))
                        <div class="alert alert-danger">
                            {{ $error }}
                        </div>
                    @else
                        <form method="POST" action="{{ route('attendance.submit') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            
                            <div class="form-group">
                                <label>Token</label>
                                <input type="text" class="form-control" value="{{ $token }}" readonly>
                            </div>

                            <button type="submit" class="btn btn-primary mt-3">Submit Attendance</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 