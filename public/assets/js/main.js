// ForClick function

 // Medical  Click
 const col = document.getElementById("serviceCol");
 const div = document.getElementById("medical-info");

 // Add click event listener to the button
 col.addEventListener("click", function () {
   // Toggle the visibility of the div
   if (div.classList.contains("hidden")) {
     div.classList.remove("hidden");
   } else {
     div.classList.add("hidden");
   }
 });
  // Terms  Click
  const col1 = document.getElementById("terms-col");
  const div1 = document.getElementById("terms-info");
 
  // Add click event listener to the button
  col1.addEventListener("click", function () {
    // Toggle the visibility of the div
    if (div1.classList.contains("hidden")) {
      div1.classList.remove("hidden");
    } else {
      div1.classList.add("hidden");
    }
  });
    // Plans  Click
    const col2 = document.getElementById("plans-col");
    const div2 = document.getElementById("plans-info");
   
    // Add click event listener to the button
    col2.addEventListener("click", function () {
      // Toggle the visibility of the div
      if (div2.classList.contains("hidden")) {
        div2.classList.remove("hidden");
      } else {
        div2.classList.add("hidden");
      }
    });
      // Terms  Click
  const col3 = document.getElementById("non-col");
  const div3 = document.getElementById("non-info");
 
  // Add click event listener to the button
  col3.addEventListener("click", function () {
    // Toggle the visibility of the div
    if (div3.classList.contains("hidden")) {
      div3.classList.remove("hidden");
    } else {
      div3.classList.add("hidden");
    }
  });