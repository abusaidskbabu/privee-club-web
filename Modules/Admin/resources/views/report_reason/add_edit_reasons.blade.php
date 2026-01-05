@extends('admin::layouts.master')
@section('css')
@endsection
@section('content')
<div class="content-wrapper">
   <div class="content-header">
      <div class="container-fluid">
         <div class="page-header" >
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <h3><i class="las la-user-plus"></i>Add/Update Report Reason</h3>
                <button class="btn btn-primary  text-black">
                    <a href="{{ route('admin.report.reason') }}" class="back-btn">← Back</a>
                </button>
             </div>
         </div>
      </div>
   </div>
   <section class="content">
      <div class="container-fluid">
         <div class="tab-content" id="genderTabsContent">
            <div class="tab-pane fade show active" id="male" role="tabpanel" aria-labelledby="male-tab">
               <div class="card mt-3">
                  <div class="card-body table-responsive">
                     <form action="{{ route('admin.add.update.reasons', isset($report_reason) ? base64_encode($report_reason->id) : '') }}" method="POST">
                       @csrf
                       <div class="form-group mb-3">
                            <label for="reason">Reason <span class="text-danger">*</span></label>
                            <input type="text" name="reason" class="form-control"
                               value="{{ old('reason', isset($report_reason) ? $report_reason->report_reason : '') }}"
                               placeholder="Enter reason">
    
                           @error('reason')
                               <span class="text-danger">{{ $message }}</span>
                           @enderror
                       </div>
    
                       <button type="submit" class="btn btn-success">
                           {{ isset($report_reason) && $report_reason->id != '' ? 'Update Reason' : 'Add Reason' }}
                       </button>
                   </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
</div>
@endsection