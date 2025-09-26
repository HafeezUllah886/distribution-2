@extends('layout.popups')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header row">
                    <div class="col-6"><h3> Forward Cheque </h3></div>
                    <div class="col-6 d-flex flex-row-reverse"><a href="{{route('cheques.index')}}" class="btn btn-danger">Close</a></div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                    <div class="row">
                        <div class='col-md-6'>
                            <table class="table">
                                <tr>
                                    <td><b>Cheque Number</b></td>
                                    <td>{{$cheque->number}}</td>
                                </tr>
                                <tr>
                                    <td><b>Cheque Clearing Date</b></td>
                                    <td>{{$cheque->cheque_date}}</td>
                                </tr>
                                <tr>
                                    <td><b>Bank</b></td>
                                    <td>{{$cheque->bank}}</td>
                                </tr>
                                <tr>
                                    <td><b>Amount</b></td>
                                    <td>{{$cheque->amount}}</td>
                                </tr>
                                <tr>
                                    <td><b>Received From</b></td>
                                    <td>{{$cheque->customer->title}}</td>
                                </tr>
                                <tr>
                                    <td><b>Receiving Order Booker</b></td>
                                    <td>{{$cheque->orderbooker->name}}</td>
                                </tr>
                                <tr>
                                    <td><b>Receiving Notes</b></td>
                                    <td>{{$cheque->notes}}</td>
                                </tr>
                            </table>
                           {{--  <dl class="row">
                                <dt class="col-sm-3">Cheque Number</dt>
                                <dd class="col-sm-9">{{$cheque->number}}</dd>
                              
                                <dt class="col-sm-3">Cheque Clearing Date</dt>
                                <dd class="col-sm-9">{{$cheque->cheque_date}}</dd>

                                <dt class="col-sm-3">Bank</dt>
                                <dd class="col-sm-9">{{$cheque->bank}}</dd>
                              
                                <dt class="col-sm-3">Amount</dt>
                                <dd class="col-sm-9">{{$cheque->amount}}</dd>
                              
                                <dt class="col-sm-3">Received From</dt>
                                <dd class="col-sm-9">{{$cheque->customer->title}}</dd>

                                <dt class="col-sm-3">Receiving Order Booker</dt>
                                <dd class="col-sm-9">{{$cheque->orderbooker->name}}</dd>

                                <dt class="col-sm-3">Receiving Notes</dt>
                                <dd class="col-sm-9">{{$cheque->notes}}</dd>
                              </dl> --}}
                        </div>
                        <div class='col-md-6'>
                            <form id="forwardForm" method="post" action="{{ route('cheques.forward') }}" enctype="multipart/form-data">
                                @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Forward To</label>
                                        <select name="account" id="account" class="selectize">
                                            @foreach ($accounts as $account)
                                                <option value="{{$account->id}}">{{$account->title}} - {{$account->type}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Orderbooker</label>
                                        <select name="orderbookerID" id="orderbookerID" class="selectize">
                                            @foreach ($orderbookers as $booker)
                                                <option value="{{$booker->id}}">{{$booker->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Forward Date</label>
                                        <input type="date" name="forwardedDate" required value="{{ date('Y-m-d') }}" id="forwardedDate" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Forward Notes</label>
                                        <textarea name="forwardedNotes" id="forwardedNotes" class="form-control"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Attachment</label>
                                        <input type="file" name="file" id="file" class="form-control">
                                    </div>
                                    <input type="hidden" name="id" id="cheque_id" value="{{ $cheque->id }}">
                                <button type="submit" class="btn btn-primary w-100">Forward</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
    </div>
    <!-- Default Modals -->
@endsection

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/libs/selectize/selectize.min.css') }}">
@endsection

@section('page-js')
<script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
   
   <script>
    $(document).ready(function() {
        $('.selectize').selectize();
    });
   </script>
@endsection
