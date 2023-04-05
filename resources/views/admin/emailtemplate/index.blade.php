@extends('admin.layout.default')

@section('title', __('supplier.template_email'))

@section('stylesheets')
<style type="text/css">
    .btn-circle {
        padding: 7px 10px;
        border-radius: 50%; 
        font-size: 1rem;
    }
</style>
@endsection

@section('content')
<!-- Header -->
<div class="header {{env('PAINELCOR')}} pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
        </div>
    </div>
</div>
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-12 mb-5 mb-xl-0">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                        <h3 class="mb-0">{{ trans('supplier.template_email') }}</h3>
                        </div>
                        <div class="col">
                            <div class="float-right">                                
                          
                        
                        </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <!-- Projects table -->
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">{{ trans('supplier.description') }}</th>
                                <th scope="col" class="actions-th">{{ trans('supplier.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($emails as $email)
                            
                            <tr>
                                <td>
                                    {{ $email->name }}
                                </td>
                                <td class="actions-td">
                                      <a href="{{ route('admin.emailtemplate.edit', $email->id) }}" class="btn btn-info btn-circle" role='button'><i class="fas fa-pencil-alt"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                               
                            </tr>
                            @endforelse
                        </tbody>
                          
                        </tbody>
                    </table>
                </div>
                <div class="card-footer py-4">
                    <div class="float-right">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
   
</div>
@endsection

@section('scripts')
    <script type="text/javascript">
        function update_delete_form_action(action){
            $("#delete_form").attr('action', action);
        }
    </script>
@endsection
