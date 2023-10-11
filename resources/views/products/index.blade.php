@extends('products.layout')



@section('content')
    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2>Yoprint - Interview</h2>

            </div>

            <div class="pull-right">

                <a class="btn btn-success" href="{{ route('products.create') }}"> Create New Product</a>

            </div>

        </div>

    </div>



    @if ($message = Session::get('success'))
        <div class="alert alert-success">

            <p>{{ $message }}</p>

        </div>
    @endif

    @if ($message = Session::get('failed'))
        <div class="alert alert-success">

            <p>{{ $message }}</p>

        </div>
    @endif

    <div class="card-body">
        <form id="csvForm" action="{{ route('products.importCSV') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="csvFile" class="form-control">
            <br>
            <button class="btn btn-success">Import User Data</button>
            <br>
        </form>
    </div>
    <br><br>
    <table class="table table-bordered">

        <tr>

            <th>Time</th>

            <th>Filename</th>

            <th>Status</th>

        </tr>

        @php ($i = 0) <!-- Initialize $i -->
        @foreach ($filelist as $file)
            <tr>
                <td>{{ $file->uploaded_at }} <br> ( {{ \Carbon\Carbon::now()->diffInMinutes($file->uploaded_at) }} minutes ago )</td>
                <td>{{ $file->fu_name }}</td>
                <td>{{ GetUploadStatus($file->fu_status) }}</td>
            </tr>
        @endforeach
        

    </table>



    {{-- {!! $products->links() !!} --}}


    <script>
        $(document).ready(function() {
            $(".delete-product").click(function(e) {
                e.preventDefault();

                var product_id = $(this).data("id");
                var token = $("[name='_token']").val();

                $.ajax({
                    url: "{{ route('products.destroy') }}",
                    type: 'POST',
                    data: {
                        "product_id": product_id,
                        "_token": token,
                    },
                    success: function(response) {
                        if (response.valid == true) {
                            alert("Deleted");
                            location.reload();
                        } else {
                            alert("Failed");
                        }
                    },
                    error: function(xhr, valid, error) {
                        console.error('An error occurred:', error);
                    }
                });
            });

            $("#csvForm").on("submit", function (e){
                var token = $("[name='_token']").val();
                e.preventDefault();
                $.ajax({
                    url : $(this).attr("action"),
                    type : $(this).attr("method"),
                    data : {
                        "csvFile" : $("[name='csvFile']").val()
                        "_token": token,
                    },
                    dataType : "json",
                    cache : false,
                    processData  : false,
                    contentType : false,
                    success : function (response){
                        console.log(response);
                    }
                })
            })
        });
    </script>
@endsection
