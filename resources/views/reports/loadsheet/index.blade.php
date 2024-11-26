@extends('layout.app')
@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>View Load Sheet Report</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="orderbooker">Order Booker</label>
                        <select name="orderbooker" class="form-control" id="orderbookerID">
                            @foreach ($orderbookers as $man)
                                <option value="{{$man->id}}">{{$man->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label for="date">Date</label>
                        <input type="date" name="date" id="date" value="{{date('Y-m-d')}}" class="form-control">
                    </div>

                    <div class="form-group mt-2">
                        <button class="btn btn-success w-100" id="viewBtn">View Report</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('page-js')

    <script>

        $("#viewBtn").on("click", function (){
            var date = $("#date").val();
            var id = $("#orderbookerID").find("option:selected").val();
            console.log(id);
            var url = "{{ route('reportLoadsheetData', ['id' => ':id','date' => ':date' ]) }}"
        .replace(':date', date)
        .replace(':id', id);
            window.open(url, "_blank", "width=1000,height=800");
        });
    </script>
@endsection
