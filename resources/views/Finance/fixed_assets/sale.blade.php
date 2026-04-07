@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Sale Fixed Asset | {{ $asset->item_description }}</h3>
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
                    <form action="{{ route('fixed_asset.sale', $asset->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ $asset->id }}">
                        @include('layout.payment')

                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="date" name="date" value="{{ $asset->date }}" id="date"
                                class="form-control">
                        </div>
                        <div class="form-group mt-2">
                            <label for="notes">Notes</label>
                            <textarea name="notes" required id="notes" cols="30" class="form-control" rows="5"></textarea>
                        </div>


                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-secondary w-100">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- Default Modals -->


@endsection
