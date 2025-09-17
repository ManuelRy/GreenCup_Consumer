@extends('master')

@section('content')
  <div class="container-fluid min-vh-100 py-3">
    <div class="row justify-content-center">
      <div class="col-12 col-xl-8">

        <!-- Page Header -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white rounded-4">
              <div class="card-body py-4 text-center">
                <div class="mb-3">
                  <i class="fas fa-exclamation-triangle fa-3x opacity-90"></i>
                </div>
                <h2 class="fw-bold mb-2">Report an Issue</h2>
                <p class="fw-light opacity-90 mb-0">Help us improve your <span class="h5 font-weight-bold">Green Cups</span> experience</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Report Form -->
        <div class="row">
          <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
              <div class="card-header bg-white border-0 pb-0">
                <h5 class="fw-semibold text-dark mb-0">
                  <i class="fas fa-edit text-primary me-2"></i>
                  Submit Report
                </h5>
              </div>
              <div class="card-body">
                <!-- Display Success Message -->
                @if(session('success'))
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                @endif

                <!-- Display Validation Errors -->
                @if($errors->any())
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                      @foreach($errors->all() as $error)
                        <li>
                          @if(str_contains($error, 'image') && str_contains($error, 'may not be greater than'))
                            <strong>Image too large:</strong> Please choose an image smaller than 5MB
                          @elseif(str_contains($error, 'image') && str_contains($error, 'must be an image'))
                            <strong>Invalid file type:</strong> Please upload a valid image file (JPG, PNG, GIF)
                          @else
                            {{ $error }}
                          @endif
                        </li>
                      @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                @endif

                <form method="POST" action="{{ route('report.store') }}" enctype="multipart/form-data">
                  @csrf

                  <!-- Report Type Selection -->
                  <div class="row mb-4">
                    <div class="col-12">
                      <label class="form-label fw-semibold text-dark">
                        <i class="fas fa-list me-2"></i>What type of issue are you reporting?
                      </label>
                      <div class="row g-3">
                        <div class="col-6 col-md-4">
                          <input type="radio" class="btn-check" name="tag" id="app-bug" value="App Bug" required {{ old('tag') == 'App Bug' ? 'checked' : '' }}>
                          <label class="btn btn-outline-primary w-100 py-3 report-type-btn" for="app-bug">
                            <div class="fs-2 mb-2">
                              <i class="fas fa-bug"></i>
                            </div>
                            <div class="fw-semibold small">App Bug</div>
                          </label>
                        </div>
                        <div class="col-6 col-md-4">
                          <input type="radio" class="btn-check" name="tag" id="store-issue" value="Store Issue" required {{ old('tag') == 'Store Issue' ? 'checked' : '' }}>
                          <label class="btn btn-outline-primary w-100 py-3 report-type-btn" for="store-issue">
                            <div class="fs-2 mb-2">
                              <i class="fas fa-store"></i>
                            </div>
                            <div class="fw-semibold small">Store Issue</div>
                          </label>
                        </div>
                        <div class="col-6 col-md-4">
                          <input type="radio" class="btn-check" name="tag" id="payment-problem" value="Payment" required {{ old('tag') == 'Payment' ? 'checked' : '' }}>
                          <label class="btn btn-outline-primary w-100 py-3 report-type-btn" for="payment-problem">
                            <div class="fs-2 mb-2">
                              <i class="fas fa-credit-card"></i>
                            </div>
                            <div class="fw-semibold small">Payment</div>
                          </label>
                        </div>
                        <div class="col-6 col-md-4">
                          <input type="radio" class="btn-check" name="tag" id="account-problem" value="Account" required {{ old('tag') == 'Account' ? 'checked' : '' }}>
                          <label class="btn btn-outline-primary w-100 py-3 report-type-btn" for="account-problem">
                            <div class="fs-2 mb-2">
                              <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="fw-semibold small">Account</div>
                          </label>
                        </div>
                        <div class="col-6 col-md-4">
                          <input type="radio" class="btn-check" name="tag" id="scanning-issue" value="QR Scan" required {{ old('tag') == 'QR Scan' ? 'checked' : '' }}>
                          <label class="btn btn-outline-primary w-100 py-3 report-type-btn" for="scanning-issue">
                            <div class="fs-2 mb-2">
                              <i class="fas fa-qrcode"></i>
                            </div>
                            <div class="fw-semibold small">QR Scan</div>
                          </label>
                        </div>
                        <div class="col-6 col-md-4">
                          <input type="radio" class="btn-check" name="tag" id="other" value="Other" required {{ old('tag') == 'Other' ? 'checked' : '' }}>
                          <label class="btn btn-outline-primary w-100 py-3 report-type-btn" for="other">
                            <div class="fs-2 mb-2">
                              <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="fw-semibold small">Other</div>
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Priority Level -->
                  <div class="row mb-4">
                    <div class="col-12">
                      <label class="form-label fw-semibold text-dark">
                        <i class="fas fa-exclamation-circle me-2"></i>How urgent is this issue?
                      </label>
                      <div class="row g-2">
                        <div class="col-6 col-md-3">
                          <input type="radio" class="btn-check" name="priority" id="low" value="Low" required {{ old('priority') == 'Low' ? 'checked' : '' }}>
                          <label class="btn btn-outline-success w-100 py-2" for="low">
                            <i class="fas fa-circle me-1"></i>Low
                          </label>
                        </div>
                        <div class="col-6 col-md-3">
                          <input type="radio" class="btn-check" name="priority" id="medium" value="Medium" required {{ old('priority') == 'Medium' ? 'checked' : '' }}>
                          <label class="btn btn-outline-primary w-100 py-2" for="medium">
                            <i class="fas fa-circle me-1"></i>Medium
                          </label>
                        </div>
                        <div class="col-6 col-md-3">
                          <input type="radio" class="btn-check" name="priority" id="high" value="High" required {{ old('priority') == 'High' ? 'checked' : '' }}>
                          <label class="btn btn-outline-warning w-100 py-2" for="high">
                            <i class="fas fa-circle me-1"></i>High
                          </label>
                        </div>
                        <div class="col-6 col-md-3">
                          <input type="radio" class="btn-check" name="priority" id="critical" value="Critical" required {{ old('priority') == 'Critical' ? 'checked' : '' }}>
                          <label class="btn btn-outline-danger w-100 py-2" for="critical">
                            <i class="fas fa-circle me-1"></i>Critical
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Report Title -->
                  <div class="row mb-4">
                    <div class="col-12">
                      <label for="title" class="form-label fw-semibold text-dark">
                        <i class="fas fa-heading me-2"></i>Issue Title
                      </label>
                      <input type="text" class="form-control form-control-lg" id="title" name="title" placeholder="Brief summary of the issue..." maxlength="100"
                        value="{{ old('title') }}" required>
                      <div class="form-text">
                        <small class="text-muted">
                          <span id="titleCounter">0</span>/100 characters
                        </small>
                      </div>
                    </div>
                  </div>

                  <!-- Detailed Description -->
                  <div class="row mb-4">
                    <div class="col-12">
                      <label for="description" class="form-label fw-semibold text-dark">
                        <i class="fas fa-align-left me-2"></i>Detailed Description
                      </label>
                      <textarea class="form-control" id="description" name="description" rows="6"
                        placeholder="Please provide detailed information about the issue. Include:&#10;• What were you trying to do?&#10;• What went wrong?&#10;• When did this happen?&#10;• Any error messages you saw?&#10;• Steps to reproduce the issue (if applicable)"
                        maxlength="1000" required>{{ old('description') }}</textarea>
                      <div class="form-text">
                        <small class="text-muted">
                          <span id="descCounter">0</span>/1000 characters
                        </small>
                      </div>
                    </div>
                  </div>

                  <!-- Image Upload Section -->
                  <div class="row mb-4">
                    <div class="col-12">
                      <label class="form-label fw-semibold text-dark">
                        <i class="fas fa-camera me-2"></i>Attach Screenshot or Photo (optional)
                      </label>
                      <div class="upload-area border-2 border-dashed rounded-3 p-4 text-center position-relative">
                        <input class="d-none" type="file" id="image" name="image" accept="image/*">
                        <div id="uploadContent">
                          <div class="mb-3">
                            <i class="fas fa-cloud-upload-alt fa-3x text-primary opacity-75"></i>
                          </div>
                          <h6 class="fw-semibold text-dark mb-2">Drop your image here or click to browse</h6>
                          <p class="text-muted small mb-3">
                            Screenshots help us understand your issue better
                          </p>
                          <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('image').click()">
                            <i class="fas fa-plus me-2"></i>Choose File
                          </button>
                          <div class="mt-2">
                            <small class="text-muted">JPG, PNG, GIF • Max 5MB</small>
                          </div>
                          <div class="mt-2">
                            <div id="uploadProgress" class="progress d-none" style="height: 4px;">
                              <div class="progress-bar bg-primary" role="progressbar" style="width: 0%"></div>
                            </div>
                          </div>
                        </div>
                        <div id="imagePreview" class="d-none">
                          <div class="position-relative d-inline-block">
                            <img id="previewImg" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle"
                              style="transform: translate(50%, -50%); width: 30px; height: 30px; padding: 0;" onclick="removeImage()">
                              <i class="fas fa-times"></i>
                            </button>
                          </div>
                          <div class="mt-2">
                            <small id="fileName" class="text-muted fw-medium"></small>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Submit Actions -->
                  <div class="row">
                    <div class="col-12">
                      <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('report.index') }}" class="btn btn-outline-secondary">
                          <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                          <i class="fas fa-paper-plane me-2"></i>Submit Report
                        </button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    /* Custom CSS consistent with dashboard theme */
    :root {
      --bs-primary: #1dd1a1;
      --bs-primary-rgb: 29, 209, 161;
      --bs-success: #22c55e;
      --bs-danger: #ef4444;
      --bs-warning: #f59e0b;
      --bs-info: #06b6d4;
    }

    .bg-gradient-primary {
      background: linear-gradient(135deg, #1dd1a1, #10ac84) !important;
    }

    .btn-primary {
      background: linear-gradient(135deg, #1dd1a1, #10ac84);
      border: none;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(29, 209, 161, 0.3);
      background: linear-gradient(135deg, #10ac84, #0e8e71);
    }

    .btn-outline-primary {
      border-color: #1dd1a1;
      color: #1dd1a1;
      transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
      background: #1dd1a1;
      border-color: #1dd1a1;
      transform: translateY(-1px);
    }

    .btn-outline-primary:checked {
      background: #1dd1a1;
      border-color: #1dd1a1;
      color: white;
    }

    .report-type-btn {
      transition: all 0.3s ease;
      height: 100px;
    }

    .report-type-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .btn-check:checked+.report-type-btn {
      background: #1dd1a1;
      border-color: #1dd1a1;
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(29, 209, 161, 0.3);
    }

    /* Form enhancements */
    .form-control {
      border-radius: 0.5rem;
      border: 2px solid #e9ecef;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: #1dd1a1;
      box-shadow: 0 0 0 0.2rem rgba(29, 209, 161, 0.25);
    }

    /* Priority buttons */
    .btn-outline-success:checked {
      background: #22c55e;
      border-color: #22c55e;
    }

    .btn-outline-warning:checked {
      background: #f59e0b;
      border-color: #f59e0b;
    }

    .btn-outline-danger:checked {
      background: #ef4444;
      border-color: #ef4444;
    }

    /* Card animations */
    .card {
      animation: slideUp 0.6s ease-out;
      transition: all 0.3s ease;
    }

    .card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12) !important;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Character counters */
    #titleCounter,
    #descCounter {
      font-weight: 600;
      color: #1dd1a1;
    }

    /* Enhanced Upload Area */
    .upload-area {
      border-color: #d1d5db !important;
      background: #fafafa;
      transition: all 0.3s ease;
      cursor: pointer;
      min-height: 180px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .upload-area:hover {
      border-color: #1dd1a1 !important;
      background: rgba(29, 209, 161, 0.02);
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .upload-area.dragover {
      border-color: #1dd1a1 !important;
      background: rgba(29, 209, 161, 0.05);
      transform: scale(1.02);
    }

    .upload-area.has-file {
      border-color: #22c55e !important;
      background: rgba(34, 197, 94, 0.02);
    }

    #imagePreview img {
      border: 3px solid #fff;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    /* Mobile optimizations */
    @media (max-width: 768px) {
      .container-fluid {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
      }

      .report-type-btn {
        height: 80px;
        padding: 0.5rem !important;
      }

      .report-type-btn .fs-2 {
        font-size: 1.5rem !important;
      }

      .card-body {
        padding: 1rem;
      }
    }

    /* Loading state */
    .btn:disabled {
      opacity: 0.6;
      transform: none !important;
    }

    /* Focus indicators for accessibility */
    .btn:focus,
    .form-control:focus {
      outline: 2px solid #1dd1a1;
      outline-offset: 2px;
    }

    /* Enhanced visual feedback */
    .form-check-input:checked {
      background-color: #1dd1a1;
      border-color: #1dd1a1;
    }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Character counters
      const titleInput = document.getElementById('title');
      const descInput = document.getElementById('description');
      const titleCounter = document.getElementById('titleCounter');
      const descCounter = document.getElementById('descCounter');

      function updateCounter(input, counter) {
        const count = input.value.length;
        const max = parseInt(input.getAttribute('maxlength'));
        counter.textContent = count;

        // Color coding
        if (count > max * 0.9) {
          counter.style.color = '#ef4444';
        } else if (count > max * 0.7) {
          counter.style.color = '#f59e0b';
        } else {
          counter.style.color = '#1dd1a1';
        }
      }

      titleInput.addEventListener('input', () => updateCounter(titleInput, titleCounter));
      descInput.addEventListener('input', () => updateCounter(descInput, descCounter));

      // Enhanced Image Upload Functionality
      const uploadArea = document.querySelector('.upload-area');
      const fileInput = document.getElementById('image');
      const uploadContent = document.getElementById('uploadContent');
      const imagePreview = document.getElementById('imagePreview');
      const previewImg = document.getElementById('previewImg');
      const fileName = document.getElementById('fileName');

      // Click to upload
      uploadArea.addEventListener('click', () => {
        if (!uploadArea.classList.contains('has-file')) {
          fileInput.click();
        }
      });

      // Drag and drop functionality
      uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
      });

      uploadArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
      });

      uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
          handleFileSelection(files[0]);
        }
      });

      // File input change
      fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
          handleFileSelection(e.target.files[0]);
        }
      });

      // Handle file selection
      function handleFileSelection(file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
          showAlert('Please select an image file (JPG, PNG, GIF)', 'warning');
          fileInput.value = ''; // Clear the input
          return;
        }

        // Validate file size (5MB = 5,242,880 bytes)
        const maxSize = 5 * 1024 * 1024;
        if (file.size > maxSize) {
          const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
          showAlert(`Image is too large (${fileSizeMB}MB). Please choose an image smaller than 5MB.`, 'danger');
          fileInput.value = ''; // Clear the input
          return;
        }

        // Create file reader
        const reader = new FileReader();

        // Show upload progress
        const uploadProgress = document.getElementById('uploadProgress');
        const progressBar = uploadProgress.querySelector('.progress-bar');
        uploadProgress.classList.remove('d-none');

        // Simulate progress for user feedback
        let progress = 0;
        const progressInterval = setInterval(() => {
          progress += Math.random() * 30;
          if (progress > 90) progress = 90;
          progressBar.style.width = progress + '%';
        }, 100);

        reader.onload = (e) => {
          // Complete progress
          clearInterval(progressInterval);
          progressBar.style.width = '100%';

          setTimeout(() => {
            previewImg.src = e.target.result;
            fileName.textContent = file.name;

            // Show preview, hide upload content
            uploadContent.classList.add('d-none');
            imagePreview.classList.remove('d-none');
            uploadArea.classList.add('has-file');
            uploadProgress.classList.add('d-none');
            progressBar.style.width = '0%';

            // Add success feedback
            uploadArea.style.borderColor = '#22c55e';
            showAlert(`Image "${file.name}" uploaded successfully!`, 'success');
          }, 500);
        };

        reader.onerror = () => {
          clearInterval(progressInterval);
          uploadProgress.classList.add('d-none');
          progressBar.style.width = '0%';
          showAlert('Failed to process the image. Please try again.', 'danger');
          fileInput.value = '';
        };

        reader.readAsDataURL(file);
      }

      // Remove image function (global scope)
      window.removeImage = function() {
        fileInput.value = '';
        previewImg.src = '';
        fileName.textContent = '';

        // Show upload content, hide preview
        uploadContent.classList.remove('d-none');
        imagePreview.classList.add('d-none');
        uploadArea.classList.remove('has-file');
        uploadArea.style.borderColor = '';
      };

      // Enhanced alert function
      function showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 90px; right: 20px; z-index: 9999; max-width: 400px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);';

        let icon = 'info-circle';
        if (type === 'warning') icon = 'exclamation-triangle';
        else if (type === 'danger') icon = 'times-circle';
        else if (type === 'success') icon = 'check-circle';

        alertDiv.innerHTML = `
            <i class="fas fa-${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);

        // Auto remove after 6 seconds for longer messages
        setTimeout(() => {
          if (alertDiv.parentNode) {
            alertDiv.remove();
          }
        }, 6000);
      }

      // Basic form interaction - Remove the preventDefault to allow actual submission
      const form = document.querySelector('form');
      const submitBtn = form.querySelector('button[type="submit"]');

      form.addEventListener('submit', function(e) {
        // Add loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';

        // Don't prevent default - let the form submit normally
      });

      // Animate cards on scroll
      const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
      };

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
          }
        });
      }, observerOptions);

      // Observe all cards
      document.querySelectorAll('.card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `all 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
      });

      // Touch feedback for mobile
      if ('ontouchstart' in window) {
        document.addEventListener('touchstart', function(e) {
          if (e.target.closest('.report-type-btn, .btn')) {
            e.target.closest('.report-type-btn, .btn').style.transform = 'scale(0.98)';
          }
        }, {
          passive: true
        });

        document.addEventListener('touchend', function(e) {
          if (e.target.closest('.report-type-btn, .btn')) {
            setTimeout(() => {
              const btn = e.target.closest('.report-type-btn, .btn');
              if (btn && !btn.classList.contains('btn-check:checked')) {
                btn.style.transform = '';
              }
            }, 100);
          }
        }, {
          passive: true
        });
      }
    });
  </script>
@endsection
