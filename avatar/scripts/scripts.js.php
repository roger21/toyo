<?php

ini_set("display_errors", "0");
ini_set("display_startup_errors", "0");
ini_set("error_log", "../errors/errors.log");
ini_set("error_log_mode", 0600);
ini_set("error_reporting", "-1");
ini_set("html_errors", "0");
ini_set("log_errors", "1");
ini_set("log_errors_max_len", "0");

//trigger_error("scripts.js.php ".var_export($_REQUEST, true));

header("Content-Type: application/javascript");

?>
let ias = null;
let currentFile = null;
let timer = null;

let generatePreview = function(selection) {
  $("#infos_area_div").css("display", "none");
  if($("#preview_area").css("display") !== "block") {
    $("#wait_generate_div").css("display", "flex");
  } else {
    $("#image_preview").attr("src", "img/wait.gif");
  }
  if(typeof currentFile["url"] !== "undefined")
    selection["url"] = currentFile["url"];
  if(typeof currentFile["fileName"] !== "undefined")
    selection["fileName"] = currentFile["fileName"];
  $.ajax({
    url: "generateAvatar.php" + (sharpenEnabled() ? "?sharpen=sharpen" : ""),
    data: selection,
    complete: function(jqxhr) {
      $("#wait_generate_div").css("display", "none");
      let extra = "?foo=" + new Date().getTime();
      let avatarFileName = jqxhr.responseText;
      let error = false;
      if(avatarFileName === "") {
        avatarFileName = "./img/error.png";
        error = true;
      }
      $("#image_preview").attr("src", avatarFileName + extra);
      $("#image_preview").attr("title", error ? "error" : null);
      $("#preview_area").css("display", "block");
      $("#bbcode_input").css("display", "block");
      // aurait.eu
      $("#bbcode_input").val(error ? "error" :
        "[img]https://hfr-rehost.aurait.eu/https://" +
        "<?php echo $_SERVER['SERVER_NAME']; ?>" +
        avatarFileName.substring(1) + extra + "[/img]");
      // diberie.com
      $("#bbcode_input").val(error ? "error" :
        "[img]https://rehost.diberie.com/Rehost?url=" +
        encodeURIComponent("https://<?php echo $_SERVER['SERVER_NAME']; ?>" +
          avatarFileName.substring(1) + extra) + "[/img]");
      // reho.st
      $("#bbcode_input").val(error ? "error" :
        "[img]https://reho.st/https://" +
        "<?php echo $_SERVER['SERVER_NAME']; ?>" +
        avatarFileName.substring(1) + extra + "[/img]");
    }
  });
}

let getRatio = function() {
  let left = parseInt($("#ratio_left").val(), 10);
  let right = parseInt($("#ratio_right").val(), 10);
  return ($("#lock_ratio").prop("checked") &&
      left > 0 && left < 100 && right > 0 && right < 100) ?
    "" + left + ":" + right : null;
}

let sharpenEnabled = function() {
  return $("#sharpen").prop("checked");
}

let updateRatio = function() {
  window.clearTimeout(timer);
  timer = window.setTimeout(function() {
    let ratio = getRatio();
    ias.setOptions({
      "aspectRatio": ratio
    });
    if(ratio !== null) {
      let r = parseInt($("#ratio_left").val(), 10) / parseInt($("#ratio_right").val(), 10);
      let selection = ias.getSelection(true);
      let cr = selection.width / selection.height;
      if(cr > r)
        selection.x2 = parseInt(selection.x1 + (selection.height * r), 10) - 1;
      if(cr < r)
        selection.y2 = parseInt(selection.y1 + (selection.width / r), 10) - 1;
      ias.setSelection(selection.x1, selection.y1, selection.x2, selection.y2, true);
      ias.update();
      generatePreview(ias.getSelection());
    }
  }, 250);
}

let onSelectionChange = function(img, selection) {
  window.clearTimeout(timer);
  timer = window.setTimeout(function() {
    generatePreview(selection);
  }, 250);
};

$(document).ready(function() {
  $("#url_input").val("");
  $("#bbcode_input").val("");
  $("#lock_ratio").prop("checked", false);
  $("#sharpen").prop("checked", false);
  $("#ratio_left").val("3");
  $("#ratio_right").val("2");
  $("#ratio_left").attr("disabled", "disabled");
  $("#ratio_right").attr("disabled", "disabled");

  let options = {
    "handles": true,
    "aspectRatio": getRatio(),
    "instance": true,
    "keys": true,
    "onSelectChange": onSelectionChange
  };

  $("#url_input").on("focus", function() {
    this.select();
  });

  $("#bbcode_input").on("focus", function() {
    this.select();
  });

  $("#lock_ratio").on("click", function() {
    $("#ratio_left").attr("disabled", this.checked ? null : "disabled");
    $("#ratio_right").attr("disabled", this.checked ? null : "disabled");
    updateRatio();
  });

  $("#ratio_left").on("keyup", function() {
    updateRatio();
  });

  $("#ratio_right").on("keyup", function() {
    updateRatio();
  });

  $("#sharpen").on("click", function() {
    generatePreview(ias.getSelection());
  });

  document.querySelector("#image").addEventListener("load", function() {
    options["imageWidth"] = this.naturalWidth;
    options["imageHeight"] = this.naturalHeight;
    ias = $("#image").imgAreaSelect(options);
    $("#infos_area_div").css("display", "block");
  }, false);

  $("#go_button").on("click", function() {
    let url = $("#url_input").val().trim();
    $("#input_area").css("display", "none");
    $("#image_area").css("display", "block");
    currentFile = {
      "url": url
    };
    $("#image").attr("src", url);
  });

  new AjaxUpload($("#upload_button"), {
    action: "uploadImg.php",
    name: "tmpImg",
    onSubmit: function(file, ext) {
      if(!ext || /^(jpg|png|jpeg|gif|bmp|webp)$/i.test(ext) === false) {
        alert("Ce n'est pas une image !");
        return false;
      } else {
        $("#input_area_div").css("display", "none");
        $("#wait_upload_div").css("display", "flex");
      }
    },
    onComplete: function(file, response) {
      if(response === "")
        return;
      fileName = response;
      $("#input_area").css("display", "none");
      $("#image_area").css("display", "block");
      currentFile = {
        "fileName": fileName
      };
      $("#image").attr("src", fileName);
    }
  });
});
