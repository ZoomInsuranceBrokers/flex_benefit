  <style>
      #openVideoModal {
          position: relative;
          display: inline-block;
      }

      .poster {
          /* display: block;
          max-width: 100%;
          height: auto; */
      }

      .play-button {
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          max-width: 20%;
          box-shadow: none !important;
          /* Adjust size as needed */
      }

      .setsize {
          font-size: 1.7rem;
          font-weight: 600;
          color: var(--blue);
      }

      /* .services:hover {
          box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
      } */

      .services {
          transition: 1s ease;
      }

      .services:hover {
          -webkit-transform: scale(1.1);
          -ms-transform: scale(1.1);
          transform: scale(1.1);
          transition: 1s ease;
      }
  </style>

  <!--how it works tab content area-->
  <div class="tab-pane fade show active" id="howwork" role="tabpanel" aria-labelledby="howwork-tab">
      <div class="container">
          <div class="row">
              <h2 class="text-center setsize" style="margin-bottom: 25px;">
                  Follow the steps provided below to finalize your enrollment for the year 2023-24
              </h2>
          </div>
          <div class="row">
              <div class="col-12 d-flex  custom-video">
                  <ul class="star-icon me-2">
                      <li>
                          Sequentially navigate through each of these sections to
                          explore and select from the various available benefits. It's
                          important to note that a positive point balance is required
                          for the selection of Flexi Cash Benefits
                      </li>
                      <li>
                          Within each section, you'll find a list of different
                          benefits presented in a tabular format. Begin your selection
                          by clicking on the desired benefit and then choosing the
                          relevant option
                      </li>
                      <li>
                          For insured benefits, the names of your dependants eligible
                          for coverage will be visible in tabular form. Ensure you
                          select the dependants you wish to include in applicable
                          benefits by checking against their names
                      </li>
                      <li>
                          As you make your choices for benefits, the FlexPoints
                          utilization table will automatically reflect the updates. If
                          the cost of the insured benefits surpasses the available
                          FlexPoints, any surplus will be covered through salary
                          deduction
                      </li>

                      <li>
                          After finalizing your benefit selections and saving the
                          enrollment, navigate to the 'Summary' tab to ensure accurate
                          capture of all details
                      </li>

                      <li>
                          Click the 'Confirm enrollment' button to validate your
                          benefit choices for the plan year 2023-24. It's important to
                          note that once enrollment is confirmed, no further
                          alterations or edits can be made to your selection
                      </li>
                      <li>Below are the default base coverage category wise:</li>
                  </ul>
                  <div class="videoModal mt-4">
                      <a href="#" id="openVideoModal">
                          <img src="{{ asset('assets/images/enrolment.png') }}" class="poster"
                              style="border-radius: 25px;" alt="poster image">
                          <img src="{{ asset('assets/images/playbutton.png') }}" class="play-button" alt="poster image">
                      </a>
                  </div>
              </div>
              <!-- <div class="col-12">
                  <ul class="star-icon m-ul">


                      <li>
                          Click the 'Confirm enrollment' button to validate your
                          benefit choices for the plan year 2023-24. It's important to
                          note that once enrollment is confirmed, no further
                          alterations or edits can be made to your selection
                      </li>
                  </ul>
              </div> -->
          </div>


          <div class="col-12">

              <div class="card insurance-list border-0 mb-3">
                  <div class="table-responsive">
                      <table class="table table-bordered text-white mb-0">
                          <tbody>
                              <tr>
                                  <th scope="col">Category</th>
                                  <!-- <th scope="col">Sub-Category</th> -->
                                  <th scope="col">Core Benefit</th>
                                  <th scope="col">Sum Insured</th>
                              </tr>
                              @foreach ($data['basePlan'] as $bpRow)
                                  @php
                                      if (
                                          count($data['gradeAmtData']) &&
                                          array_key_exists(
                                              $bpRow['subcategory']['categories']['id'],
                                              $data['gradeAmtData'],
                                          )
                                      ) {
                                          $bpsa =
                                              (int) $data['gradeAmtData'][$bpRow['subcategory']['categories']['id']];
                                          $is_grade_based = true;
                                      } else {
                                          // Provided values
                                          $encryptedData = Auth::user()->salary;
                                          $encryptionKey = 'QCsmMqMwEE+Iqfv0IIXDjAqrK4SOSp3tZfCadq1KlI4=';
                                          $initializationVector = 'G4bfDHjL3gXiq5NCFFGnqQ==';

                                          // Decrypt the data
                                          $cipher = 'aes-256-cbc';
                                          $options = OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING;

                                          $salary = openssl_decrypt(
                                              base64_decode($encryptedData),
                                              $cipher,
                                              base64_decode($encryptionKey),
                                              $options,
                                              base64_decode($initializationVector),
                                          );

                                          if ($salary === false) {
                                              echo 'Error during decryption: ' . openssl_error_string() . PHP_EOL;
                                          } else {
                                              $salary = floatval(rtrim($salary, "\0"));
                                          }

                                          $sa = !is_null($bpRow['sum_insured']) ? $bpRow['sum_insured'] : 0;
                                          $sa_si = !is_null($bpRow['si_factor'])
                                              ? ($sa_si = $bpRow['si_factor'] * $salary)
                                              : 0;
                                          if ($sa_si > $sa) {
                                              $bpsa = (int) $sa_si;
                                              $is_si_sa = true;
                                              $base_si_factor = $bpRow['si_factor'];
                                          } else {
                                              $bpsa = (int) $sa;
                                              $is_sa = true;
                                          }
                                      }
                                      // name of base policy
                                      $bpName = $bpRow['name'];
                                  @endphp
                                  <tr>
                                      <td scope="row">{{ $bpRow['subcategory']['categories']['name'] }}</td>
                                      <!-- <td>Medical-IPD</td> -->
                                      <td>{{ $bpRow['name'] }}</td>
                                      <td>{{ number_format(round($bpsa), 0, '.', ',') }}</td>
                                  </tr>
                              @endforeach
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>
      @if ($data['is_enrollment_window'])
          <div class="col-12 medical__Services">
              <div class="row" style="padding: 5vw;">
                  @foreach ($data['category'] as $item)
                      @if ($item['name'] == 'Medical')
                          <div class="col-lg-3 plans-col tablinks1" data-bs-target="#content-tab-{{ $item['id'] }}">
                              <div class="services" onclick="activateTab('{{ $item['id'] }}')">
                                  <div class="card-items">
                                      <div class="card-list">
                                          <div class="service-img">
                                              <img src="{{ asset('assets/images/close-up-doctor-paper-family.jpg') }}" alt=""
                                                  class="service-main m-0 p-0" />
                                          </div>
                                          <!-- <div class="img-icon">
                                        <img src="{{ asset('assets/images/icon-img.png') }}" alt="icon" />
                                    </div> -->
                                          <h3>Medical Insurance</h3>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      @elseif($item['name'] == 'Accidental')
                          <div class="col-lg-3 terms-col tablinks1" data-bs-target="#content-tab-{{ $item['id'] }}">
                              <div class="services" onclick="activateTab('{{ $item['id'] }}')">
                                  <div class="card-items">
                                      <div class="card-list">
                                          <div class="service-img">
                                              <img src="{{ asset('assets/images/col-img4.jpg') }}" alt=""
                                                  class="service-main m-0 p-0" />
                                          </div>
                                          <!-- <div class="img-icon">
                                        <img src="{{ asset('assets/images/icon-img.png') }}" alt="icon" />
                                    </div> -->
                                          <h3>Accident Insurance</h3>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      @elseif($item['name'] == 'Term Life')
                          <div class="col-lg-3 service-col tablinks1"
                              data-bs-target="#content-tab-{{ $item['id'] }}">
                              <div class="services" onclick="activateTab('{{ $item['id'] }}')">
                                  <div class="card-items">
                                      <div class="card-list">
                                          <div class="service-img">
                                              <img src="{{ asset('assets/images/term-life.jpg') }}" alt=""
                                                  class="service-main m-0 p-0" />
                                          </div>
                                          <!-- <div class="img-icon">
                                        <img src="{{ asset('assets/images/icon-img.png') }}" alt="icon" />
                                    </div> -->
                                          <h3>Life Insurance</h3>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      @elseif(trim($item['name']) == 'Non-Insured Benefits')
                          <div class="col-lg-3 non-col tablinks1" data-bs-target="#content-tab-{{ $item['id'] }}">
                              <div class="services" onclick="activateTab('{{ $item['id'] }}')">
                                  <div class="card-items">
                                      <div class="card-list">
                                          <div class="service-img">
                                              <img src="{{ asset('assets/images/main-banner3.jpeg') }}" alt=""
                                                  class="service-main m-0 p-0" />
                                          </div>
                                          <!-- <div class="img-icon">
                                        <img src="{{ asset('assets/images/icon-img.png') }}" alt="icon" />
                                    </div> -->
                                          <h3>Non-Insured Benefits</h3>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      @endif
                  @endforeach
              </div>
          </div>


      @endif
  </div>
  <!-- Modal Popup for Video -->
  <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
          <div>
              <div class="modal-content">
                  <div class="modal-body">
                      <div class="col-12">
                          <video id="myVideo" height="400" controls
                              controlsList="nofullscreen nodownload nopicure-in-picture">
                              <source src="{{ asset('assets/videos/enrollment.mp4') }}" type="video/mp4">
                          </video>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <!--End Modal Popup for Video -->
  <script>
      document.getElementById('openVideoModal').addEventListener('click', function(event) {
          event.preventDefault();
          $('#videoModal').modal('show'); // Show the modal
          document.getElementById('myVideo').play(); // Play the video
      });

      $(document).ready(function() {
          $('#videoModal').on('hidden.bs.modal', function() {
              document.getElementById('myVideo').pause(); // Pause the video
          });
      });
      //   var video = document.getElementById('videoPlayer');

      //   function toggleFullScreen() {
      //       if (!document.fullscreenElement) {
      //           video.requestFullscreen();
      //       } else {
      //           if (document.exitFullscreen) {
      //               document.exitFullscreen();
      //           }
      //       }
      //   }

      //   video.addEventListener('click', function() {
      //       if (video.paused) {
      //           video.play();
      //       } else {
      //           video.pause();
      //       }
      //   });

      //   document.querySelector('.video-2').addEventListener('click', function() {
      //       toggleFullScreen();
      //   });
  </script>

<script>
    function activateTab(tabId) {
        var tabTrigger = document.querySelector('[data-bs-target="#content-tab-' + tabId + '"]');
        tabTrigger.click();
    }
</script>


