// NOTE: If you run this file locally
// You will not get a server status
// You can comment out lines 9 and 26 to make it work locally

var xhr = new XMLHttpRequest();                 // Create XMLHttpRequest object

xhr.onload = function() {                       // When readystate changes
  // The following conditional check will not work locally - only on a server
  //if(xhr.status === 200) {                      // If server status was ok
    responseObject = JSON.parse(xhr.responseText);

    // BUILD UP STRING WITH NEW CONTENT (could also use DOM manipulation)
    var newContent = '';
    for (var i = 0; i < responseObject.staff.length; i++) { // Loop through object
      newContent += '<ul><li><div class="weui-panel weui-panel_access" id="" style="width: 96%;margin-left: 2%;border-radius: 5px;margin-top: 5px;margin-bottom: 5px;">';
      newContent += '<div class="weui-panel__bd" id="main-panel__bd">';
      newContent += '<a href="#" class="weui-media-box weui-media-box_appmsg"  onclick="opendetail'+responseObject.staff[i].number+'()">';
      newContent += '<div class="weui-media-box__bd">';
      newContent += '<p name="teaName" id="h4style">'+responseObject.staff[i].name+'</p>';
      newContent += '<p id="h4stylep">'+responseObject.staff[i].college+','+responseObject.staff[i].major+'，';
      newContent += '职称'+responseObject.staff[i].level+'，';
      newContent += '代表教学课程：'+responseObject.staff[i].class+'<p id="h4stylep">';
      newContent += '讲课质量分：'+responseObject.staff[i].score+'负责程度值：';
      newContent += responseObject.staff[i].responsibility+'<span class="blankagain">...</span>';
      newContent += '</p></div></a></div></div></li></ul>';
    }

    // Update the page with the new content
    document.getElementById('wrap').innerHTML = newContent;
  //}
};

xhr.open('GET', 'staff.json', true);        // Prepare the request
xhr.send(null);                                 // Send the request

// When working locally in Firefox, you may see an error saying that the JSON is not well-formed.
// This is because Firefox is not reading the correct MIME type (and it can safely be ignored).

// If you get it on a server, you may need to se the MIME type for JSON on the server (application/JSON).