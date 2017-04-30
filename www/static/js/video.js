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

});
