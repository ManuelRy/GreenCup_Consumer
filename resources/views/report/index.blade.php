@extends('master')

@section('content')
  <div class="container-fluid min-vh-100 py-3">
    <div class="row justify-content-center">
      <div class="col-12 col-xl-10">

        <!-- Page Header -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card border-0 shadow-sm text-white rounded-4" style="background: linear-gradient(135deg, #1dd1a1, #10ac84);">
              <div class="card-body py-4">
                <div class="d-flex align-items-center justify-content-between">
                  <div>
                    <div class="mb-3">
                      <i class="fas fa-list-alt fa-3x opacity-75"></i>
                    </div>
                    <h2 class="fw-bold mb-2">My Reports</h2>
                    <p class="fw-light opacity-90 mb-0">Track the status of your submitted reports</p>
                  </div>
                  <div class="text-end">
                    <a href="{{ route('report.create') }}" class="btn btn-light btn-lg shadow-sm">
                      <i class="fas fa-plus me-2"></i>New Report
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
          <div class="row mb-4">
            <div class="col-12">
              <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            </div>
          </div>
        @endif

        <!-- Reports List -->
        <div class="row">
          <div class="col-12">
            @if($reports && $reports->count() > 0)
              <div class="row g-4">
                @foreach($reports as $report)
                  <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3 h-100 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 border-start border-4" style="border-left-color: #1dd1a1 !important;">
                      <div class="card-body p-4">
                        <div class="row align-items-center">
                          <div class="col-md-8">
                            <div class="d-flex align-items-start">
                              <!-- Report Icon Based on Tag -->
                              <div class="me-3">
                                @if($report->tag == 'App Bug')
                                  <div class="d-flex align-items-center justify-content-center bg-danger text-white rounded-circle" style="width: 50px; height: 50px;">
                                    <i class="fas fa-bug"></i>
                                  </div>
                                @elseif($report->tag == 'Store Issue')
                                  <div class="d-flex align-items-center justify-content-center bg-warning text-white rounded-circle" style="width: 50px; height: 50px;">
                                    <i class="fas fa-store"></i>
                                  </div>
                                @elseif($report->tag == 'Payment')
                                  <div class="d-flex align-items-center justify-content-center bg-info text-white rounded-circle" style="width: 50px; height: 50px;">
                                    <i class="fas fa-credit-card"></i>
                                  </div>
                                @elseif($report->tag == 'Account')
                                  <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-circle" style="width: 50px; height: 50px;">
                                    <i class="fas fa-user-circle"></i>
                                  </div>
                                @elseif($report->tag == 'QR Scan')
                                  <div class="d-flex align-items-center justify-content-center bg-success text-white rounded-circle" style="width: 50px; height: 50px;">
                                    <i class="fas fa-qrcode"></i>
                                  </div>
                                @else
                                  <div class="d-flex align-items-center justify-content-center bg-secondary text-white rounded-circle" style="width: 50px; height: 50px;">
                                    <i class="fas fa-question-circle"></i>
                                  </div>
                                @endif
                              </div>

                              <!-- Report Details -->
                              <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2 flex-wrap">
                                  <h5 class="mb-0 fw-bold text-dark me-3">{{ $report->title }}</h5>

                                  <!-- Priority Badge -->
                                  @if($report->priority == 'Critical')
                                    <span class="badge bg-danger d-inline-flex align-items-center">
                                      <i class="fas fa-exclamation-triangle me-1"></i>Critical
                                    </span>
                                  @elseif($report->priority == 'High')
                                    <span class="badge bg-warning d-inline-flex align-items-center">
                                      <i class="fas fa-exclamation-circle me-1"></i>High
                                    </span>
                                  @elseif($report->priority == 'Medium')
                                    <span class="badge bg-primary d-inline-flex align-items-center">
                                      <i class="fas fa-circle me-1"></i>Medium
                                    </span>
                                  @else
                                    <span class="badge bg-success d-inline-flex align-items-center">
                                      <i class="fas fa-circle me-1"></i>Low
                                    </span>
                                  @endif
                                </div>

                                <!-- Tag and Date -->
                                <div class="mb-2 d-flex flex-wrap align-items-center gap-2">
                                  <span class="badge bg-light text-dark border">
                                    <i class="fas fa-tag me-1"></i>{{ $report->tag }}
                                  </span>
                                  <small class="text-muted d-flex align-items-center">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $report->created_at->format('M d, Y \a\t g:i A') }}
                                  </small>
                                </div>

                                <!-- Description Preview -->
                                <p class="text-muted mb-0 lh-base">
                                  {{ Str::limit($report->description, 120) }}
                                </p>
                              </div>
                            </div>
                          </div>

                          <!-- Status and Actions -->
                          <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <!-- Status Badge -->
                            @if($report->status == 'Resolve')
                              <div class="badge bg-success text-white px-3 py-2 d-inline-flex align-items-center mb-2">
                                <i class="fas fa-check-circle me-2"></i>
                                <span>Resolved</span>
                              </div>
                            @elseif($report->status == 'Warning')
                              <div class="badge bg-warning text-white px-3 py-2 d-inline-flex align-items-center mb-2">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <span>Under Review</span>
                              </div>
                            @elseif($report->status == 'Investigate')
                              <div class="badge bg-info text-white px-3 py-2 d-inline-flex align-items-center mb-2">
                                <i class="fas fa-search me-2"></i>
                                <span>Investigating</span>
                              </div>
                            @elseif($report->status == 'Suspend')
                              <div class="badge bg-danger text-white px-3 py-2 d-inline-flex align-items-center mb-2">
                                <i class="fas fa-pause-circle me-2"></i>
                                <span>Suspended</span>
                              </div>
                            @else
                              <div class="badge bg-secondary text-white px-3 py-2 d-inline-flex align-items-center mb-2">
                                <i class="fas fa-clock me-2"></i>
                                <span>Pending</span>
                              </div>
                            @endif

                            <!-- Report ID -->
                            <div class="mt-2">
                              <small class="text-muted">
                                Report #{{ str_pad($report->id, 6, '0', STR_PAD_LEFT) }}
                              </small>
                            </div>
                          </div>
                        </div>

                        <!-- Evidences -->
                        @if($report->evidences && $report->evidences->count() > 0)
                          <div class="row mt-3">
                            <div class="col-12">
                              <hr class="my-3">
                              <div class="d-flex align-items-center mb-3">
                                <small class="text-muted me-3">
                                  <i class="fas fa-paperclip me-1"></i>Attachments ({{ $report->evidences->count() }}):
                                </small>
                              </div>
                              <div class="d-flex gap-2 flex-wrap">
                                @foreach($report->evidences as $index => $evidence)
                                  <div class="position-relative overflow-hidden rounded shadow-sm"
                                       role="button"
                                       tabindex="0"
                                       style="width: 80px; height: 80px; cursor: pointer; transition: all 0.3s ease;"
                                       onmouseover="this.style.transform='scale(1.05)'; this.querySelector('.overlay').style.opacity='1'"
                                       onmouseout="this.style.transform='scale(1)'; this.querySelector('.overlay').style.opacity='0'"
                                       onclick="showImageMobile('{{ $evidence->file_url }}', 'Evidence {{ $index + 1 }} - Report #{{ str_pad($report->id, 6, '0', STR_PAD_LEFT) }}')">

                                    <img src="{{ $evidence->file_url }}"
                                         alt="Evidence {{ $index + 1 }}"
                                         class="w-100 h-100 object-fit-cover border rounded"
                                         style="border-color: #e9ecef !important;">

                                    <div class="overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-black bg-opacity-60 rounded"
                                         style="opacity: 0; transition: opacity 0.3s ease;">
                                      <i class="fas fa-search-plus text-white bg-dark bg-opacity-75 rounded-circle p-2"></i>
                                    </div>
                                  </div>
                                @endforeach
                              </div>
                            </div>
                          </div>
                        @endif
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>

              <!-- Pagination -->
              @if($reports->hasPages())
                <div class="row mt-5">
                  <div class="col-12">
                    <div class="d-flex justify-content-center">
                      {{ $reports->links() }}
                    </div>
                  </div>
                </div>
              @endif

            @else
              <!-- Empty State -->
              <div class="row">
                <div class="col-12">
                  <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body text-center py-5">
                      <div class="mb-4">
                        <i class="fas fa-inbox fa-4x text-muted opacity-50"></i>
                      </div>
                      <h4 class="fw-bold text-dark mb-3">No Reports Yet</h4>
                      <p class="text-muted mb-4">
                        You haven't submitted any reports yet. If you encounter any issues or have feedback,
                        please don't hesitate to submit a report.
                      </p>
                      <a href="{{ route('report.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>Submit Your First Report
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            @endif
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Bootstrap Modal for Image Lightbox - Mobile Optimized -->
  <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down modal-xl modal-dialog-centered">
      <div class="modal-content bg-dark border-0">
        <!-- Floating close button - positioned absolutely -->
        <button type="button"
                class="btn btn-danger position-absolute top-0 end-0 m-3 rounded-circle d-flex align-items-center justify-content-center shadow-lg"
                data-bs-dismiss="modal"
                aria-label="Close"
                style="width: 60px; height: 60px; font-size: 28px; z-index: 9999; border: 3px solid white; font-weight: bold;">
          ×
        </button>

        <!-- Mobile-friendly header -->
        <div class="modal-header border-0 p-3 pe-5">
          <div id="modalTitleHeader" class="text-white fs-6 fw-bold"></div>
        </div>

        <div class="modal-body p-3 text-center d-flex flex-column justify-content-center">
          <img id="modalImage"
               src=""
               alt=""
               class="img-fluid rounded shadow-lg mb-3"
               style="max-height: 70vh; width: auto; max-width: 100%;">

          <!-- Swipe indicator for mobile -->
          <div class="d-block d-sm-none text-white-50 small mt-3">
            <i class="fas fa-chevron-down me-2"></i>Swipe down or tap the red button to close
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    /* Minimal custom CSS - mostly for transitions and specific styling */
    :root {
      --bs-primary: #1dd1a1;
      --bs-primary-rgb: 29, 209, 161;
    }

    .transition-all {
      transition: all 0.3s ease;
    }

    .hover\:shadow-lg:hover {
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1) !important;
    }

    .hover\:-translate-y-1:hover {
      transform: translateY(-5px);
    }

    .btn-primary {
      background: linear-gradient(135deg, #1dd1a1, #10ac84);
      border: none;
    }

    .btn-primary:hover {
      background: linear-gradient(135deg, #10ac84, #0e8e71);
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(29, 209, 161, 0.3);
    }

    .btn-light:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    /* Pagination styling */
    .pagination .page-link {
      color: #1dd1a1;
    }

    .pagination .page-link:hover {
      color: #10ac84;
      background-color: rgba(29, 209, 161, 0.1);
      border-color: #1dd1a1;
    }

    .pagination .page-item.active .page-link {
      background-color: #1dd1a1;
      border-color: #1dd1a1;
    }
  </style>

  <script>
    // Mobile-optimized image modal
    function showImageMobile(src, title) {
      document.getElementById('modalImage').src = src;
      document.getElementById('modalTitleHeader').textContent = title;

      // Show modal
      const modal = new bootstrap.Modal(document.getElementById('imageModal'));
      modal.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
      // Auto-dismiss alerts after 5 seconds
      setTimeout(() => {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(alert => {
          const closeBtn = alert.querySelector('.btn-close');
          if (closeBtn) {
            closeBtn.click();
          }
        });
      }, 5000);

      // Enhanced keyboard navigation for thumbnails
      document.querySelectorAll('[role="button"][tabindex="0"]').forEach(thumbnail => {
        thumbnail.addEventListener('keydown', function(e) {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            this.click();
          }
        });
      });

      // Mobile swipe to close modal
      let startY = 0;
      let currentY = 0;
      let isDragging = false;

      const modal = document.getElementById('imageModal');
      const modalImage = document.getElementById('modalImage');

      modal.addEventListener('touchstart', function(e) {
        if (window.innerWidth <= 576) { // Only on mobile
          startY = e.touches[0].clientY;
          isDragging = true;
        }
      });

      modal.addEventListener('touchmove', function(e) {
        if (!isDragging || window.innerWidth > 576) return;

        currentY = e.touches[0].clientY;
        const diffY = currentY - startY;

        if (diffY > 0) { // Only allow downward swipe
          modalImage.style.transform = `translateY(${diffY * 0.5}px)`;
          modal.style.opacity = Math.max(0.3, 1 - (diffY / 300));
        }
      });

      modal.addEventListener('touchend', function(e) {
        if (!isDragging || window.innerWidth > 576) return;

        const diffY = currentY - startY;

        if (diffY > 100) { // Swipe down threshold
          bootstrap.Modal.getInstance(modal).hide();
        } else {
          // Reset position
          modalImage.style.transform = 'translateY(0)';
          modal.style.opacity = '1';
        }

        isDragging = false;
      });

      // Reset styles when modal is hidden
      modal.addEventListener('hidden.bs.modal', function() {
        modalImage.style.transform = 'translateY(0)';
        modal.style.opacity = '1';
      });

      // Add haptic feedback on mobile (if supported)
      document.querySelectorAll('[role="button"]').forEach(button => {
        button.addEventListener('touchstart', function() {
          if ('vibrate' in navigator) {
            navigator.vibrate(10); // Short haptic feedback
          }
        });
      });
    });
  </script>

  <!-- Bootstrap JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
@endsection
