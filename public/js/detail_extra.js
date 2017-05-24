var xhr = new XMLHttpRequest();                 // Create XMLHttpRequest object

xhr.onload = function() {                       // When readystate changes
  // The following conditional check will not work locally - only on a server
  //if(xhr.status === 200) {                      // If server status was ok
    responseObject = JSON.parse(xhr.responseText);

    // BUILD UP STRING WITH NEW CONTENT (could also use DOM manipulation)
    var newContent = '';
    var newComment = '';
    for (var i = 0; i < responseObject.staff.length; i++) { // Loop through object
      newContent += '<p class="titlelist"><span id="staffname">'+responseObject.staff[i].name+'</span><span>';
      newContent += '</span>|<span id="staffclass">'+responseObject.staff[i].level+'</span><span class="correct" id="correctWrong" style="display:" onclick=correctWrong()>纠正信息</span><span class="correct" style="display:none;" onclick=submitWrong() id="submitWrong">提交</span></p>';
      newContent += '<div class="whole"><div class="context"><p>学院:<span>'+responseObject.staff[i].college+'</span></p>';
      newContent += '<p>专业:<span>'+responseObject.staff[i].major+'</span></p>';
      newContent += '<p>职称:<span>'+responseObject.staff[i].level+'</span></p>';
      newContent += '<p>代表课程:<span id="wrongClass" style="display:">'+responseObject.staff[i].class+'</span><input placeholder="国际经济学双语" class="correctclass" id="correct_class_box" style="display:none"></p>';
      newContent += '<p>联系方式:<span id="wrongContact" style="display:">'+responseObject.staff[i].contact+'</span><input placeholder="13111111111" class="correctcontact" id="correct_contact_box" style="display:none"></p>';
      newContent += '</div></div></div>'
      newContent += '<div class="middle" id="middle"><div class="weui-slider-box" id="slider1"><div class="weui-slider"><div id="sliderInner" class="weui-slider__inner"><div id="sliderTrack" style="width: '+responseObject.staff[i].score+'%;" class="weui-slider__track"></div>'
      newContent += '<div id="sliderHandler" style="left: '+responseObject.staff[i].score+'%;" class="weui-slider__handler"></div></div></div>'
      newContent += '<div id="sliderValue" class="weui-slider-box__value">'+responseObject.staff[i].score+'</div></div><div class="score"><span>讲课质量分：</span></div>'
      newContent += '<div class="weui-slider-box" id="slider2"><div class="weui-slider"><div id="sliderInner" class="weui-slider__inner"><div id="sliderTrack" style="width: '+responseObject.staff[i].responsibility+'%;" class="weui-slider__track"></div>'
      newContent += '<div id="sliderHandler" style="left: '+responseObject.staff[i].responsibility+'%;" class="weui-slider__handler"></div></div></div>'
      newContent += '<div id="sliderValue" class="weui-slider-box__value">'+responseObject.staff[i].responsibility+'</div></div><div class="score"><span>负责程度值：</span></div>'
      newContent += '<div class="weui-slider-box" id="slider3"><div class="weui-slider"><div id="sliderInner" class="weui-slider__inner"><div id="sliderTrack" style="width: '+responseObject.staff[i].pass+'%;" class="weui-slider__track"></div>'
      newContent += '<div id="sliderHandler" style="left: '+responseObject.staff[i].pass+'%;" class="weui-slider__handler"></div></div></div>'
      newContent += '<div id="sliderValue" class="weui-slider-box__value">'+responseObject.staff[i].pass+'</div></div><div class="score"><span>难过难易度：</span></div></div>'
      newComment += '<div  id="oldword" style="font-size: 14px;"><div><div style="float: left;width: 20%;">';
      newComment += '<div class="face"></div></div><div style="float: left;width: 80%;"><div id="user_name" style=""><span class="user_name_style" style="color: #787878">'+responseObject.staff[i].userName+'</span></div>';
      newComment += '<div id="context" style=""><span class="context_style">'+responseObject.staff[i].context+'</span></div>';
      newComment += '<div id="user_time" style=""><span style="color: #9e9e9e;font-size: 12px;">'+responseObject.staff[i].time+'</span></div></div></div></div>';
    }

    // Update the page with the new content
    document.getElementById('whole').innerHTML = newContent;
    document.getElementById('oldword').innerHTML = newComment;
    console.log(newContent);
  //}
};

xhr.open('GET', 'staffy.json', true);        // Prepare the request
xhr.send(null);                                 // Send the request

// When working locally in Firefox, you may see an error saying that the JSON is not well-formed.
// This is because Firefox is not reading the correct MIME type (and it can safely be ignored).

// If you get it on a server, you may need to se the MIME type for JSON on the server (application/JSON).