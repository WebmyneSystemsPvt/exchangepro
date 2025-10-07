@extends('layouts.app')
@section('page_title', 'Create Gender')
@section('content')
    <div class="container">
        <div class="row">
             <div class="card">
                <div class="card-body">
                    <div class="col-md-6">
                        <form action="{{ route('banners.store') }}" method="POST" class="material-form" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <input type="text" required="required" name="title" id="banner"/>
                                <label for="input" class="control-label">Banner Title</label><i class="bar"></i>
                            </div>
                            <div class="form-group">
                                <textarea type="text" name="description" required="required" ></textarea>
                                <label for="input" class="control-label">Banner Description</label><i class="bar"></i>
                            </div>
                            <div class="form-group">
                                <input type="file" name="photo[]" class="form-control" multiple />
                                <label for="input" class="control-label">Banner Image</label><i class="bar"></i>
                            </div>
{{--                             <div class="form-group">--}}
{{--                                    <label for="active" class="font-weight-medium font-weight-500 flag-color">Status</label>--}}
{{--                                    <div class="row text-right">--}}
{{--                                        <div class="col-4">--}}
{{--                                            <label for="active">Active</label>--}}
{{--                                        </div>--}}
{{--                                        <div class="col-8">--}}
{{--                                            <input type="radio" id="active" name="status" value="1" required>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="row mt-2">--}}
{{--                                        <div class="col-4">--}}
{{--                                            <label for="inactive">Inactive</label>--}}
{{--                                        </div>--}}
{{--                                        <div class="col-8">--}}
{{--                                            <input type="radio" id="inactive" name="status" value="0" required>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                            </div>--}}
                            <div class="button-container">
                                <button type="submit" class="btn btn-primary" id="saveBtn">Submit</button>
                                <a href="{{route('banners.index')}}" class="btn btn-danger" id="backBtn">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
