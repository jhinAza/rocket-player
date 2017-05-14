$(function() {
  window.debug = true;
  video = Factory.getNewMediaController("mainVideo");
  video.setContainer(".video-container");
  video.addControl(".pause", "pause");
  video.addControl(".play", "play");
  video.addControl("#stop", "stop");
  video.addControl("#seekBar", "seekBar");
  video.addControl("#time", "timer");
  video.addControl("#fullscreen", "fullscreen");
  video.addControl("#mute", "mute");
  video.addControl("#volumeBar", "volume");
  video.addControl("#subs", "subs");
  if ($(".subtitles").length > 0) {
    video.addComponent(".subtitles", "subs", ".sub");
  }
  if ($(".transcription").length > 0) {
    video.addComponent(".transcription", "trans", ".trans");
  }
  if ($(".video-sign-lang").length > 0) {
    console.log("Porque?");
    video.addComponent(".video-sign-lang", "sign");
  }

});
