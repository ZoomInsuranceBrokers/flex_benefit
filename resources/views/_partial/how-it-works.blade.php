  <!--how it works tab content area-->
  <div class="tab-pane fade show active" id="howwork" role="tabpanel" aria-labelledby="howwork-tab">

      <style>
          .video-containers {
              position: relative;
              width: 100%;
              height: 0;
              padding-bottom: 56.25%;
              /* 16:9 aspect ratio */
          }

          video {
              position: absolute;
              top: 0;
              left: 0;
              width: 100%;
              height: 100%;
          }
      </style>
      <div class="container">
          <div class="row">
              <div class="col-12">
                  <h4 class="mt-3 mb-4">
                      Follow the steps provided below to finalize your enrollment for the plan year 2023-24:
                  </h4>
                  <div class="row">
                      <div class="col-lg-6 mb-4">
                          <ul class="star-icon">
                              <li>Sequentially navigate through each of these sections to explore and select from the
                                  various available benefits. It's important to note that a positive point balance is
                                  required for the selection of Flexi Cash Benefits</li>
                              <li>Within each section, you'll find a list of different benefits presented in a tabular
                                  format. Begin your selection by clicking on the desired benefit and then choosing the
                                  relevant option</li>
                              <li>For insured benefits, the names of your dependants eligible for coverage will be
                                  visible in tabular form. Ensure you select the dependants you wish to include in
                                  applicable benefits by checking against their names</li>
                              <li>As you make your choices for benefits, the FlexPoints utilization table will
                                  automatically reflect the updates. If the cost of the insured benefits surpasses the
                                  available FlexPoints, any surplus will be covered through salary deduction</li>
                              <li>After finalizing your benefit selections and saving the enrollment, navigate to the
                                  'Summary' tab to ensure accurate capture of all details</li>
                              <li>Click the 'Confirm enrollment' button to validate your benefit choices for the plan
                                  year 2023-24. It's important to note that once enrollment is confirmed, no further
                                  alterations or edits can be made to your selection</li>
                              <li>Health insurance for self, spouse, children, and parents falls under section 80D.
                                  However, the selection of parents-in-law is ineligible for a tax rebate under section
                                  80D.</li>
                          </ul>
                      </div>
                      <div class="col-lg-6">
                          <div class="video-containers">
                              <video id="videoPlayer" controls>
                                  <source src="{{ asset('assets/videos/enrollment.mp4') }}" type="video/mp4">
                                  Your browser does not support the video tag.
                              </video>
                          </div>
                      </div>
                  </div>
              </div>
          </div>


          <div class="col-12">
              <ul class="star-icon">
                  <li>Below are the default base coverage category wise:</li>
              </ul>
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
      <div class="col-12 medical__Services">
          <div class="row">
              <div class="col-lg-3 col-md-6">
                  <div class="card">
                      <img src="{{ asset('assets/images/col-img1-1.png') }}" alt="services" class="" />
                      <img src="{{ asset('assets/images/icon-img.png') }}" alt="star img" class="star_img" />
                      <h4>Life Insurance</h4>
                  </div>
              </div>
              <div class="col-lg-3 col-md-6 mt-3 mt-md-0">
                  <div class="card">
                      <img src="{{ asset('assets/images/col-img2.png') }}" alt="services" class="" />
                      <img src="{{ asset('assets/images/icon-img.png') }}" alt="star img" class="star_img" />
                      <h4>Accident Insurance</h4>
                  </div>
              </div>
              <div class="col-lg-3 col-md-6 mt-3 mt-lg-0">
                  <div class="card">
                      <img src="{{ asset('assets/images/col-img3.png') }}" alt="services" class="" />
                      <img src="{{ asset('assets/images/icon-img.png') }}" alt="star img" class="star_img" />
                      <h4>Medical Insurance</h4>
                  </div>
              </div>
              <div class="col-lg-3 col-md-6 mt-3 mt-lg-0">
                  <div class="card">
                      <img src="{{ asset('assets/images/col-img1.png') }}" alt="services" class="" />
                      <img src="{{ asset('assets/images/icon-img.png') }}" alt="star img" class="star_img" />
                      <h4>Flexi Cash Benefits</h4>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <script>
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