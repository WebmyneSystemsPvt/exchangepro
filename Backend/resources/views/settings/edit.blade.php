@extends('layouts.app')
@section('page_title', 'Edit Settings')
@section('content')
    <div class="container">
        <div class="row">
            <h2>Edit Settings</h2>

            <div class="col-md-6">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('settings.update', $data->id) }}" method="POST" class="material-form">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <input type="text" name="margin_percentage" id="margin_percentage" value="{{ old('margin_percentage', $data->margin_percentage) }}" required/>
                        <label for="margin_percentage" class="control-label">Margin Percentage</label><i class="bar"></i>
                    </div>
                    <div class="form-group">
                        <input type="text" name="application_fee" id="application_fee" value="{{ old('application_fee', $data->application_fee) }}" required/>
                        <label for="application_fee" class="control-label">Application Fee</label><i class="bar"></i>
                    </div>
                    <div class="form-group">
                        <input type="text" name="others_fee" id="others_fee" value="{{ old('others_fee', $data->others_fee) }}" required/>
                        <label for="others_fee" class="control-label">Others Fee</label><i class="bar"></i>
                    </div>
                    <div class="form-group">
                        <input type="text" name="tax" id="tax" value="{{ old('tax', $data->tax) }}" required/>
                        <label for="tax" class="control-label">Tax</label><i class="bar"></i>
                    </div>
                    <div class="button-container">
                        <button type="submit" class="button btn btn-primary" id="saveBtn">Submit</button>
                        <a href="{{ route('settings.index') }}" class="button btn btn-primary" id="backBtn">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
