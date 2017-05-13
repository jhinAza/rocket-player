"use strict"; //I like to use the strict mode.
// Here I'm creating the factory object
// This is with the purpose of making easier to know how to work without the need of writing additional code for this purpose.
// https://en.wikipedia.org/wiki/Factory_method_pattern
var FactoryMediaController = function() {}
FactoryMediaController.prototype.constructor = FactoryMediaController;
FactoryMediaController.prototype.getNewMediaController = function(id) {
  // I was thinking to use the user agent string, but it seems I'm not able anymore. So I'm using duck typing instead
  // https://en.wikipedia.org/wiki/Duck_typing
  if (document.webkitFullscreenEnabled) {
    return new WebkitMediaController(id);
  } else if (document.mozFullScreenEnabled) {
    return new GeckoMediaController(id);
  } else if (document.msFullscreenEnabled) {
    return new IEMediaController(id);
  } else {
    return new GenericMediaController(id);
  }
}
//GenericMediaController is not only the generic type, also is the prototype in which are based the other prototypes
function GenericMediaController(id){
  this.id = id; //ID of the main media element
  this.media = document.getElementById(id); //Vanilla JS element
  this.$media = $(this.media); //jQuery element.
  if (this.$media.is("video")) {
    this.type = "video";
  } else if (this.$media.is("audio")) {
    this.type = "audio";
  }
  this.engine = "unknown"; //This will be overrided in the specialized constructors
  this.controls = {}; //For storing the custom controls when it's needed.
  this.controls.override = {}; //If we want to use the standar behaviour of the library.
  this.components = {}; //For storing additional media we want to sync.
  this.currentSubIndex = 0;
  this.setEvents();
  if (debug) {
    console.log(this); //Debugging only.
  }
}
GenericMediaController.prototype.constructor = GenericMediaController;
GenericMediaController.prototype.stop = function () {
  //Pause the video and returns to the very beggining.
  this.media.pause();
  this.media.currentTime = 0;
};
GenericMediaController.prototype.play = function() {
  //Pauses the media.
  //TODO: Update the function to pause others media object if any.
  if (!this.isPlaying()) {
    this.media.play();
  }
}
GenericMediaController.prototype.mute = function () {
  if (this.isMuted()) {
    volume = document.querySelector(this.controls.volume).value;
    this.setVolume(parseFloat(volume));
  } else {
    this.setVolume(0);
  }
};
GenericMediaController.prototype.pause = function () {
  //Just like the play() function I need to update it when I add support for multiple media objects.
  if (this.isPlaying()) {
    this.media.pause();
  }
};
GenericMediaController.prototype.toString = function() {
  //Debugging only.
  return this.media.toString() + this.engine;
};
GenericMediaController.prototype.isMuted = function () {
  return this.media.volume == 0;
};
GenericMediaController.prototype.isPlaying = function(){
  return !this.media.paused;
}
GenericMediaController.prototype.isFullscreen = function () {
  return this.container === document.fullscreenElement
};
GenericMediaController.prototype.hasAutoPlay = function() {
  return this.$media.attr("autoplay") != undefined;
}
GenericMediaController.prototype.getSize = function () {
  //Returns a tuple composed of the real height and real width of the element.
  return [this.getHeight(), this.getWidth()];
};
GenericMediaController.prototype.getWidth = function() {
  //Return the real width of the element composed of the width of the elements, and left and right borders, magins and paddings.
  var width = this.$media.css("width");
  var borderLeft = this.$media.css("border-left-width");
  var borderRight =  this.$media.css("border-right-width");
  var paddingLeft = this.$media.css("padding-left");
  var paddingRight = this.$media.css("padding-right");
  var marginLeft = this.$media.css("margin-left");
  var marginRight = this.$media.css("margin-right");
  width = parseInt(width.substr(0, width.indexOf("px")));
  borderLeft = borderLeft.substr(0, borderLeft.indexOf("px"));
  borderRight = borderRight.substr(0, borderRight.indexOf("px"));
  paddingLeft = paddingLeft.substr(0, paddingLeft.indexOf("px"));
  paddingRight = paddingRight.substr(0, paddingRight.indexOf("px"));
  marginLeft = marginLeft.substr(0, marginLeft.indexOf("px"));
  marginRight = marginRight.substr(0, marginRight.indexOf("px"));
  var border = parseInt(borderRight) + parseInt(borderLeft);
  var padding = parseInt(paddingRight) + parseInt(paddingLeft);
  var margin = parseInt(marginRight) + parseInt(marginLeft);
  return width + margin + padding + border;
}
GenericMediaController.prototype.getHeight = function() {
  //Return the real width of the element composed of the width of the elements, and left and right borders, magins and paddings.
  var width = this.$media.css("height");
  var borderTop = this.$media.css("border-top-width");
  var borderBottom =  this.$media.css("border-bottom-width");
  var paddingTop = this.$media.css("padding-top");
  var paddingBottom = this.$media.css("padding-bottom");
  var marginTop = this.$media.css("margin-top");
  var marginBottom = this.$media.css("margin-bottom");
  width = parseInt(width.substr(0, width.indexOf("px")));
  borderTop = borderTop.substr(0, borderTop.indexOf("px"));
  borderBottom = borderBottom.substr(0, borderBottom.indexOf("px"));
  paddingTop = paddingTop.substr(0, paddingTop.indexOf("px"));
  paddingBottom = paddingBottom.substr(0, paddingBottom.indexOf("px"));
  marginTop = marginTop.substr(0, marginTop.indexOf("px"));
  marginBottom = marginBottom.substr(0, marginBottom.indexOf("px"));
  var border = parseInt(borderBottom) + parseInt(borderTop);
  var padding = parseInt(paddingBottom) + parseInt(paddingTop);
  var margin = parseInt(marginBottom) + parseInt(marginTop);
  return width + margin + padding + border;
}
GenericMediaController.prototype.getDuration = function () {
  return this.media.duration;
};
GenericMediaController.prototype.getCurrentTime = function () {
  return this.media.currentTime;
};
GenericMediaController.prototype.getStringTime = function () {
  //Returns a string composed of the current time of the media using hour, minutes and also seconds.
  var time = parseInt(this.getCurrentTime());
  var hours = Math.floor(time / 3600);
  time = time - (hours * 3600);
  var minutes = Math.floor(time / 60);
  var seconds = time - (minutes * 60);
  var timeStr = "";
  if (minutes < 10) {
    timeStr += 0 + minutes.toString();
  } else {
    timeStr += minutes.toString();
  }
  if (seconds < 10) {
    timeStr += ":" +  0 + seconds.toString();
  } else {
    timeStr += ":" + seconds.toString();
  }
  if (hours > 0) {
    timeStr = hours + ":" + timeStr;
  }
  return timeStr;
};
GenericMediaController.prototype.setTime = function (value) {
  //Change the currentTime of the media.
  //TODO: Update when the support of multiple media elements is added.
  // Maybe I can use the event for this purpose?
  this.media.currentTime = value;
};
GenericMediaController.prototype.setEvents = function () {
  //Seting the events for correct work.
  //May I use the events fired on play, pause, change and similar for controlling the additional media objects?
  var THIS = this;
  if (!this.controls.override.play) {
    this.$media.on("play", function(e) {
      $(THIS.controls.play).hide();
      $(THIS.controls.pause).show();
    });
  }
  if (!this.controls.override.pause) {
    this.$media.on("pause", function(e) {
      $(THIS.controls.play).show();
      $(THIS.controls.pause).hide();
    });
  }
  if (!this.controls.override.volume) {
    this.$media.on("volumechange", function(e) {
      if (THIS.media.volume == 0) {
        $(THIS.controls.mute).children().hide();
        $(THIS.controls.mute).children(".no-volume").show();
      } else if (THIS.media.volume > 0 && THIS.media.volume < 1) {
        $(THIS.controls.mute).children().hide();
        $(THIS.controls.mute).children(".half-volume").show();
      } else {
        $(THIS.controls.mute).children().hide();
        $(THIS.controls.mute).children(".high-volume").show();
      }
    });
  }
  this.$media.on("timeupdate", function(e){
    // console.log(THIS.getCurrentTime());
    if (THIS.controls.seekBar) {
      $(THIS.controls.seekBar).val(THIS.getCurrentTime() / THIS.getDuration());
    }
    if (THIS.controls.timer) {
      THIS.getStringTime();
      $(THIS.controls.timer).text(THIS.getStringTime());
    }
    if (THIS.components.subs) {
      THIS.updateSubs();
    }
  });
  this.$media.on("keypress", function(e) {
    if (e.which === 32) {
      if (THIS.isPlaying()) {
        THIS.pause();
      } else {
        THIS.play();
      }
      e.preventDefault();
      e.stopPropagation();
      return false;
    } else if (e.keyCode === 39) {
      var time = THIS.getCurrentTime();
      THIS.setTime(time+5);
    } else if (e.keyCode === 37) {
      var time = THIS.getCurrentTime();
      THIS.setTime(time-5);
    }
  });
  this.$media.click(function (e) {
    if (e.which === 1) {
      if (THIS.isPlaying()) {
        THIS.pause();
      } else {
        THIS.play();
      }
      e.preventDefault();
      return false;
    }
  });
};
GenericMediaController.prototype.setVolume = function (value) {
  this.media.volume = value;
};
GenericMediaController.prototype.setContainer = function(element) {
  this.container = document.querySelector(element);
  this.$container = $(element);
}
GenericMediaController.prototype.setFullScreen = function() {
  if (document.fullscreenEnabled) {
    this.container.requestFullscreen();
    this.$container.css("background-color", "black");
    this.$container.addClass("fullscreen");
  } else {
    this.$media.addClass("fullscreen");
  }
}
GenericMediaController.prototype.exitFullScreen = function() {
  if (document.fullscreenEnabled) {
    document.exitFullscreen();
  } else {
    this.$media.removeClass("fullscreen");
  }
}
GenericMediaController.prototype.addControl = function (id, type, override = false) {
  //TODO: using override we can let the user control if the library changes the buttons. If so there is a class based structure that must be followed.
  if($(id)) { //Checking if there is an element with the id received.
    const THIS = this;
    switch (type) {
      case "play"://override
        $(id).click(function() {
          THIS.play();
        });
        this.controls.play = id;
        this.controls.override.play = override;
        if (this.hasAutoPlay()) {
          $(this.controls.play).hide();
        }
        break;
      case "pause": //override
        $(id).click(function() {
          THIS.pause();
        });
        if (!this.hasAutoPlay()) {
          $(this.controls.pause).hide();
        }
        this.controls.pause = id;
        this.controls.override.pause = override;
        break;
      case "stop":
        $(id).click(function() {
          THIS.stop();
        });
        this.controls.stop = id;
        break;
      case "seekBar":
        $(id).on("mousedown", function(e) {
          if (THIS.isPlaying()) {
            // THIS.currentSubIndex = 0;
            $(this).data("replay", true);
          }
          THIS.pause();
        });
        $(id).on("mouseup", function(e) {
          if ($(this).data("replay")) {
            THIS.play();
          }
        });
        $(id).on("change", function(e) {
          THIS.setTime(THIS.getDuration() * (this.value))
        })
        this.controls.seekBar = id;
        break;
      case "timer":
        this.controls.timer = id;
        break;
      case "fullscreen"://override
        $(id).click(function() {
          if (THIS.isFullscreen()) {
            THIS.exitFullScreen();
          } else {
            THIS.setFullScreen();
          }
        });
        this.controls.fullscreen = id;
        this.controls.override.fullscreen = override;
        break;
      case "mute"://override
        $(id).click(function() {
          THIS.mute();
        });
        this.controls.mute = id;
        this.controls.override.mute = override;
        if (!override) {
          $(this.controls.mute).children().hide();
          $(this.controls.mute).children(".high-volume").show();
        }
        break;
      case "volume":
        $(id).on("change", function(e) {
          THIS.setVolume(this.value);
        });
        volume = document.querySelector(id).value;
        this.setVolume(parseFloat(volume));
        this.controls.volume = id;
        break;
      case "subs":
        $(id).click(function() {
          THIS.toggleSubs();
        });
        this.controls.subs = id;
    }
  }
};
GenericMediaController.prototype.addComponent = function (id, type) {
  if ($(id)) {
    switch (type) {
      case 'subs':
        this.components.subs = id;
        break;
      default:

    }
  }
};
GenericMediaController.prototype.updateSubs = function () {
  const THIS = this;
  var currentTime = this.getCurrentTime();
  $.each($(".sub"), function(idx, element) {
    if (idx < this.currentSubIndex) {
      return true;
    } else {
      var startTime = parseFloat($(this).data('init'));
      var endTime = parseFloat($(this).data('end'));
      if (startTime <= currentTime && endTime >= currentTime) {
        $(".sub").hide();
        $(this).show();
        THIS.currentSubIndex = idx;
        return false;
      }
    }
  })
}
GenericMediaController.prototype.toggleSubs = function () {
  if ($(this.components.subs).is(":visible")) {
    $(this.components.subs).hide();
  } else {
    $(this.components.subs).show();
  }
}
//Here are the custom objects for the engines.
function WebkitMediaController(id) {
  GenericMediaController.call(this, id);
  this.engine = "webkit";
}
WebkitMediaController.prototype = Object.create(GenericMediaController.prototype);
WebkitMediaController.prototype.setFullScreen = function () {
  this.container.webkitRequestFullScreen();
  this.$container.addClass("fullscreen");
};
WebkitMediaController.prototype.exitFullScreen = function () {
  document.webkitExitFullscreen();
  this.$container.removeClass("fullscreen");
};
WebkitMediaController.prototype.isFullscreen = function () {
  return this.container === document.webkitFullscreenElement
};

function GeckoMediaController(id) {
  GenericMediaController.call(this, id);
  this.engine = "Gecko";
}
GeckoMediaController.prototype = Object.create(GenericMediaController.prototype);
GeckoMediaController.prototype.setFullScreen = function () {
  this.container.mozRequestFullScreen();
  this.$container.addClass("fullscreen");
}
GeckoMediaController.prototype.exitFullScreen = function () {
  document.mozCancelFullScreen();
  this.$container.removeClass("fullscreen");
}
GeckoMediaController.prototype.isFullscreen = function () {
  return this.container === document.mozFullScreenElement
};

function IEMediaController(id) {
  GenericMediaController.call(this, id);
  this.engine = "MS";
}
IEMediaController.prototype = Object.create(GenericMediaController.prototype);
IEMediaController.prototype.setFullScreen = function () {
  this.container.msRequestFullscreen();
  this.$container.addClass("fullscreen");
}
IEMediaController.prototype.exitFullScreen = function () {
  document.msExitFullscreen();
  this.$container.css("background-color", "black");
};
IEMediaController.prototype.isFullscreen = function () {
  return this.container === document.msFullscreenElement;
};

//TODO: Check Safari's behaviour
//Factory global var.
window.Factory = new FactoryMediaController();
