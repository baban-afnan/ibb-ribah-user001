<x-app-layout>
    <title>{{ config('app.name') }} - First Account Opening</title>
    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title mb-3">
                <div class="row">
                    <div class="col-sm-6 col-12">
                        <h3 class="fw-bold text-primary">First Account Opening</h3>
                        <p class="text-muted small mb-0">Open your account quickly by providing the necessary details.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-3">
            <div class="row">
                <!-- Account Opening Form -->
                <div class="col-xl-6 mb-4">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0 fw-bold text-white"><i class="bi bi-person-plus-fill me-2"></i>Account Opening Form</h5>
                        </div>

                        <div class="card-body p-4">
                            {{-- Alerts --}}
                            @if (session('message'))
                                <div class="alert alert-{{ session('status') === 'success' ? 'success' : 'danger' }} alert-dismissible fade show border-0 shadow-sm mb-4">
                                    <i class="bi bi-{{ session('status') === 'success' ? 'check-circle' : 'exclamation-triangle' }} me-2"></i>
                                    {{ session('message') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
                                    <ul class="mb-0 small">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('first-account.store') }}" enctype="multipart/form-data" class="row g-4">
                                @csrf

                                <!-- Tier Selection -->
                                <div class="col-12">
                                    <label for="service_field" class="form-label fw-bold">Select Account Tier <span class="text-danger">*</span></label>
                                    <select name="service_field" id="service_field" class="form-select border-primary-subtle" required>
                                        <option value="">-- Choose Tier --</option>
                                        @foreach($fields as $field)
                                            <option value="{{ $field->id }}" data-price="{{ $field->getPriceForUserType(auth()->user()->role ?? 'user') }}" {{ old('service_field') == $field->id ? 'selected' : '' }}>
                                                {{ $field->field_name }} (NGN {{ number_format($field->getPriceForUserType(auth()->user()->role ?? 'user')) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- BVN & NIN Row -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">BVN <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-shield-lock"></i></span>
                                        <input class="form-control" name="bvn" type="text" required
                                               placeholder="11-digit BVN"
                                               value="{{ old('bvn') }}" maxlength="11" minlength="11"
                                               pattern="[0-9]{11}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">NIN <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-person-badge"></i></span>
                                        <input class="form-control" name="nin" type="text" required
                                               placeholder="11-digit NIN"
                                               value="{{ old('nin') }}" maxlength="11" minlength="11"
                                               pattern="[0-9]{11}">
                                    </div>
                                </div>

                                <!-- Phone and Passport -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Phone Number <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-phone"></i></span>
                                        <input class="form-control" name="phone_number" type="text" required
                                               placeholder="Phone Number"
                                               value="{{ old('phone_number') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Cleared Passport <span class="text-danger">*</span></label>
                                    <input type="file" name="passport" accept="image/*" class="form-control border-primary-subtle" required>
                                    <small class="text-muted">Max size: 2MB (JPEG, PNG, JPG)</small>
                                </div>

                                <!-- Address -->
                                <div class="col-12">
                                    <label class="form-label fw-bold">Address <span class="text-danger">*</span></label>
                                    <textarea name="address" rows="2" class="form-control border-primary-subtle" placeholder="Full Home Address" required>{{ old('address') }}</textarea>
                                </div>

                                <!-- LGA & State -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">LGA <span class="text-danger">*</span></label>
                                    <input class="form-control border-primary-subtle" name="lga" type="text" required
                                           placeholder="Local Government Area"
                                           value="{{ old('lga') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">State <span class="text-danger">*</span></label>
                                    <input class="form-control border-primary-subtle" name="state" type="text" required
                                           placeholder="State"
                                           value="{{ old('state') }}">
                                </div>

                                <!-- Pricing Info Row -->
                                <div class="col-12 text-center">
                                    <label class="form-label fw-bold">Total Payable</label>
                                    <div class="alert alert-soft-warning py-2 border-0 shadow-sm mb-0">
                                        <span class="h4 fw-bold mb-0 text-dark" id="total-amount">₦0.00</span>
                                    </div>
                                </div>

                                <!-- Wallet Balance -->
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">Your Wallet Balance:</span>
                                        <span class="text-success fw-bold">₦{{ number_format($wallet->balance ?? 0, 2) }}</span>
                                    </div>
                                </div>

                                <!-- Terms Checkbox -->
                                <div class="col-12">
                                    <div class="form-check custom-checkbox">
                                        <input type="checkbox" class="form-check-input" id="termsCheck" required>
                                        <label class="form-check-label small" for="termsCheck">
                                            I confirm the accuracy of this data and agree to the account opening terms.
                                        </label>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12 d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg shadow-sm hover-up">
                                        <i class="bi bi-cloud-upload me-2"></i> Submit Account Request
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Submission History -->
                <div class="col-xl-6">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="bi bi-clock-history me-2 text-primary"></i> Submission History
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form class="row g-3 mb-4 bg-light p-3 rounded-3 border" method="GET" action="{{ route('first-account.index') }}">
                                <div class="col-md-6">
                                    <input class="form-control border-0 shadow-sm" name="search" type="text" placeholder="Reference ID..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select border-0 shadow-sm" name="status">
                                        <option value="">All Statuses</option>
                                        @foreach(['pending', 'processing', 'successful', 'failed'] as $status)
                                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-primary w-100 shadow-sm" type="submit">
                                        <i class="bi bi-filter"></i>
                                    </button>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th>#</th>
                                            <th>Reference</th>
                                            <th>Tier</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($submissions as $submission)
                                            <tr>
                                                <td class="fw-bold text-muted">{{ $loop->iteration + $submissions->firstItem() - 1 }}</td>
                                                <td><span class="text-primary fw-medium">{{ $submission->reference }}</span></td>
                                                <td>{{ $submission->service_field_name }}</td>
                                                <td>₦{{ number_format($submission->amount, 2) }}</td>
                                                <td>
                                                    <span class="badge rounded-pill bg-{{ match($submission->status) {
                                                        'successful' => 'success',
                                                        'processing' => 'primary',
                                                        'failed' => 'danger',
                                                        default => 'warning'
                                                    } }}">{{ ucfirst($submission->status) }}</span>
                                                </td>
                                                <td>
                                                    @php
                                                        $fileUrl = '';
                                                        if (!empty($submission->file_url)) {
                                                            $f = $submission->file_url;
                                                            if (preg_match('/^https?:\/\//', $f)) {
                                                                $fileUrl = $f;
                                                            } elseif (str_starts_with($f, '/storage') || str_starts_with($f, 'storage')) {
                                                                $fileUrl = asset(ltrim($f, '/'));
                                                            } else {
                                                                $fileUrl = \Illuminate\Support\Facades\Storage::url($f);
                                                            }
                                                        }
                                                    @endphp
                                                    <div class="d-flex gap-1">
                                                        <button type="button"
                                                                class="btn btn-sm btn-icon btn-outline-primary"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#commentModal"
                                                                data-comment="{{ $submission->comment ?? 'No comment yet.' }}"
                                                                data-file-url="{{ $fileUrl }}">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </button>

                                                        {{-- Note: No sync status button for First Account as it is a manual process --}}
                                                        @if($submission->status === 'successful' && $fileUrl)
                                                            <a href="{{ $fileUrl }}" 
                                                               class="btn btn-sm btn-icon btn-success" 
                                                               target="_blank" 
                                                               title="Download Result">
                                                                <i class="bi bi-download"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-5">
                                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                                    No account requests found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 d-flex justify-content-center">
                                {{ $submissions->withQueryString()->links('vendor.pagination.custom') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Comment Modal --}}
        @include('pages.comment')
    </div>

    <style>
        .hover-up:hover { transform: translateY(-3px); transition: all 0.3s ease; }
        .alert-soft-warning { background-color: #fff3cd; color: #664d03; }
        .table thead th { font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
    </style>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const fieldSelect = document.getElementById('service_field');
            const totalAmountSpan = document.getElementById('total-amount');

            fieldSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const price = selectedOption.getAttribute('data-price') || 0;
                totalAmountSpan.textContent = '₦' + parseFloat(price).toLocaleString(undefined, {minimumFractionDigits: 2});
            });

            @if (session('status') && session('message'))
                Swal.fire({
                    icon: "{{ session('status') === 'success' ? 'success' : 'error' }}",
                    title: "{{ session('status') === 'success' ? 'Great!' : 'Oops!' }}",
                    text: "{{ session('message') }}",
                    confirmButtonColor: '#3085d6',
                });
            @endif
        });
    </script>
</x-app-layout>
