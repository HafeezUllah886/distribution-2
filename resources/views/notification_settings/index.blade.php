@extends('layout.app')
@section('content')
    <div class="col-12">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card mt-3">
            <div class="card-header">
                <h3>Notification Settings</h3>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('notification_settings.store') }}" method="post">
                    @csrf

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="firstnameInput" class="form-label">Start Time</label>
                                <input type="time" class="form-control" id="firstnameInput" name="start_time"
                                    value="{{ $notificationSettings ? $notificationSettings->start_time : '' }}">
                            </div>
                        </div>
                        <!--end col-->
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="emailInput" class="form-label">End Time</label>
                                <input type="time" class="form-control" id="emailInput" name="end_time"
                                    value="{{ $notificationSettings ? $notificationSettings->end_time : '' }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="emailInput" class="form-label">Intervals</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="emailInput" name="intervals"
                                        value="{{ $notificationSettings ? $notificationSettings->intervals : '' }}">
                                    <span class="input-group-text">minutes</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="emailInput" class="form-label">Week Days</label>
                                <select name="week_days[]" id="week_days" class="selectize" multiple>
                                    <option value="">Select Week Days</option>
                                    <option value="Monday" @selected($notificationSettings && in_array('Monday', $notificationSettings->week_days ?? []))>Monday</option>
                                    <option value="Tuesday" @selected($notificationSettings && in_array('Tuesday', $notificationSettings->week_days ?? []))>Tuesday</option>
                                    <option value="Wednesday" @selected($notificationSettings && in_array('Wednesday', $notificationSettings->week_days ?? []))>Wednesday</option>
                                    <option value="Thursday" @selected($notificationSettings && in_array('Thursday', $notificationSettings->week_days ?? []))>Thursday</option>
                                    <option value="Friday" @selected($notificationSettings && in_array('Friday', $notificationSettings->week_days ?? []))>Friday</option>
                                    <option value="Saturday" @selected($notificationSettings && in_array('Saturday', $notificationSettings->week_days ?? []))>Saturday</option>
                                    <option value="Sunday" @selected($notificationSettings && in_array('Sunday', $notificationSettings->week_days ?? []))>Sunday</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end">
                                <button type="submit" class="btn btn-primary">Updates</button>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                </form>
            </div>

        </div>
    </div>
@endsection
@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/libs/selectize/selectize.min.css') }}">
@endsection
@section('page-js')
    <script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".selectize").selectize({
                plugins: ['remove_button'],
                maxItems: null,
                create: false,
                placeholder: 'Select Option...'
            });
        });
    </script>
@endsection
