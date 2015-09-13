var YOUTUBE_API_KEY = "AIzaSyB-98hnWRaBYG1ekHSU562AnSKT5PJ38pU";

Lms.youtube = {
	pidx:null,
	id:null,
	context:null,
	player:null,
	tracking:null,
	trackerType:null,
	trackerTimer:null,
	trackerTotalTime:null,
	trackerBarCount:0,
	trackerBarInterval:0,
	trackerStatusMax:0,
	lastCheckTime:-1,
	lastCheckPosition:-1,
	afkTimer:null,
	lastAfkTimer:0,
	saving:false,
	saveTimer:null,
	getVideoInfo:function(id,callback) {
		$.ajax({
			type:"GET",
			url:"https://www.googleapis.com/youtube/v3/videos?part=snippet%2CcontentDetails&key="+YOUTUBE_API_KEY+"&id="+id,
			dataType:"json",
			success:function(result) {
				callback(result.items.length == 1 ? result.items[0] : null);
			}
		});
	},
	getTime:function(time) {
		var time = parseInt(time);
		var m = Math.floor(time / 60);
		var s = time % 60;
		
		return (m < 10 ? "0"+m : m)+":"+(s < 10 ? "0"+s : s);
	},
	loadVideo:function(idx,type,token) {
		Lms.youtube.pidx = idx;
		Lms.youtube.trackerType = type;
		$("#ModuleLmsYoutubePlayer").replaceWith($("<div>").attr("id","ModuleLmsYoutubePlayer"));
		$("script[src='https://www.youtube.com/iframe_api']").remove();
		
		$.ajax({
			type:"POST",
			url:ENV.getProcessUrl("lms","getPostContext"),
			data:{idx:idx},
			dataType:"json",
			success:function(result) {
				if (result.success == true) {
					Lms.youtube.id = result.context.id;
					Lms.youtube.context = result.context;
					Lms.youtube.tracking = result.tracking;
					if (Lms.youtube.trackerType == "P") {
						Lms.youtube.tracking.tracking = Lms.youtube.tracking.tracking;
					} else {
						Lms.youtube.tracking.tracking = Lms.youtube.tracking.tracking.substring(1).split("");
					}
					if (Lms.youtube.tracking.last_position > result.context.time - 10) Lms.youtube.tracking.last_position = 0;
					Lms.youtube.trackerTotalTime = result.context.time;
					
					$("#ModuleLmsYoutubePlayer").append($("<div>").attr("id","ModuleLmsYoutubePlayerEmbeded"));
					$("head").append($("<script>").attr("src","https://www.youtube.com/iframe_api"));
				} else {
					iModule.alertMessage.show("error",result.message,5);
				}
				/*
				if (result.success == false) {
					alert("해당 동영상을 찾을 수 없습니다.");
				} else {
					if (result.CAPTION == "Y") {
						$("h3").text(result.TITLE);
						if (YOUTUBE_USE_CAPTION == "Y") {
							$("h3").append($("<a>").attr("href",INDEX_URL+"?VIDEO_ID="+Youtube.VIDEO_ID).addClass("btn btn-warning").html("<i class='fa fa-language'></i> 자막번역종료"));
						} else {
							$("h3").append($("<a>").attr("href",INDEX_URL+"?VIDEO_ID="+Youtube.VIDEO_ID+"&UC=Y").addClass("btn btn-warning").html("<i class='fa fa-language'></i> 자막번역참여"));
						}
					} else {
						$("h3").text(result.TITLE);
					}
					
					Youtube.AFK_CHECK_TIME = result.AFK_CHECK_TIME;
					Youtube.YOUTUBE_PLAYED_TIME = result.YOUTUBE_PLAYED_TIME;
					Youtube.YOUTUBE_PLAYED_COUNT = result.YOUTUBE_PLAYED_COUNT;
					
					if (result.YOUTUBE_LAST_POSITION / result.TIME * 100 > 95 || YOUTUBE_USE_CAPTION == "Y") Youtube.YOUTUBE_LAST_POSITION = 0;
					else Youtube.YOUTUBE_LAST_POSITION = result.YOUTUBE_LAST_POSITION;
					
					if (result.CHECK == "TRUE") {
						$("#Player").append($("<span>").addClass("label label-danger checking").html("진도처리중"));
						Youtube.PLAYING_CHECK = true;
					} else {
						$("#Player").append($("<span>").addClass("label label-default checking").html("진도처리하지않음"));
					}
					
					$("#"+Youtube.PLAYER_ID).append($("<div>").addClass("trackBar").append($("<div>")));
					
					$("head").append($("<script>").attr("src","https://www.youtube.com/iframe_api"));
					
					if (YOUTUBE_USE_CAPTION == "Y") {
						Youtube.loadCaption();
					} else {
						Youtube.loadComment();
					}
				}
				*/
			}
		});
	},
	initPlayer:function() {
		if (Lms.youtube.id == null) return;
		
		Lms.youtube.player = new YT.Player("ModuleLmsYoutubePlayerEmbeded",{
			width:"100%",
			height:"100%",
			videoId:Lms.youtube.id,
			playerVars:{
				"fs":0,
				"modestbranding":1,
				"autohide":2,
				"controls":0,
				"showinfo":0,
				"playsinline":1,
				"rel":0
			},
			events:{
				"onReady":function(e) {
					Lms.youtube.player.loaded = true;
					Lms.youtube.player.setVolume(100);
					
					Lms.youtube.drawTracker("init");
					Lms.youtube.drawStatus("init");
					
					var playButton = $("<button>").attr("type","button").addClass("play").html('<i class="fa fa-play"></i>');
					playButton.on("click",function() {
						Lms.youtube.player.playVideo();
					});
					$("#ModuleLmsInfoBar").append(playButton);
					
					var pauseButton = $("<button>").attr("type","button").addClass("pause").html('<i class="fa fa-pause"></i>');
					pauseButton.on("click",function() {
						Lms.youtube.player.pauseVideo();
					});
					$("#ModuleLmsInfoBar").append(pauseButton);
					
					var timer = $("<span>").addClass("timer").html('<span class="current">'+Lms.youtube.getTime(Lms.youtube.player.getCurrentTime())+'</span> / '+Lms.youtube.getTime(Lms.youtube.player.getDuration()));
					$("#ModuleLmsInfoBar").append(timer);
					
					var volume = $("<span>").addClass("volume");
					volume.append($("<i>").addClass("fa fa-volume-up"));
					
					var range = $("<div>").addClass("track").data("percent",100);
					range.append($("<div>").addClass("bar").append($("<div>")));
					var pin = $("<div>").addClass("pin");
					
					range.on("mousedown",function(event) {
						$(this).data("isDrag",true);
						var percent = Math.round((event.clientX - $(this).offset().left) / $(this).width() * 100);
						$(this).data("percent",percent);
						$(this).find("div.pin").css("left",percent+"%");
						$(this).find("div.bar > div").css("width",percent+"%");
						
						event.stopPropagation();
						event.preventDefault();
					});
					
					$(document).on("mousemove",function(event) {
						if ($("#ModuleLmsInfoBar").find("div.track").data("isDrag") == true) {
							var track = $("#ModuleLmsInfoBar").find("div.track");
							
							var percent = Math.round((event.clientX - track.offset().left) / track.width() * 100);
							percent = percent < 0 ? 0 : (percent > 100 ? 100 : percent);
							track.data("percent",percent);
							track.find("div.pin").css("left",percent+"%");
							track.find("div.bar > div").css("width",percent+"%");
							
							if (percent == 0) {
								$("#ModuleLmsInfoBar span.volume i").removeClass().addClass("fa fa-volume-off");
							} else if (percent < 50) {
								$("#ModuleLmsInfoBar span.volume i").removeClass().addClass("fa fa-volume-down");
							} else {
								$("#ModuleLmsInfoBar span.volume i").removeClass().addClass("fa fa-volume-up");
							}
							
							event.stopPropagation();
							event.preventDefault();
						}
					});
					
					$(document).on("mouseup",function() {
						if ($("#ModuleLmsInfoBar").find("div.track").data("isDrag") == true) {
							$("#ModuleLmsInfoBar").find("div.track").data("isDrag",false);
							var percent = $("#ModuleLmsInfoBar").find("div.track").data("percent");
							if (percent == 0) {
								$("#ModuleLmsInfoBar span.volume i").removeClass().addClass("fa fa-volume-off");
							} else if (percent < 50) {
								$("#ModuleLmsInfoBar span.volume i").removeClass().addClass("fa fa-volume-down");
							} else {
								$("#ModuleLmsInfoBar span.volume i").removeClass().addClass("fa fa-volume-up");
							}
							Lms.youtube.player.setVolume(percent);
						}
					});
					
					range.append(pin);
					volume.append(range);
					$("#ModuleLmsInfoBar").append(volume);
					
					$("#ModuleLmsInfoBar").append($("<button>").attr("type","button").attr("data-speed","0.5").addClass("speed").html("x0.5"));
					$("#ModuleLmsInfoBar").append($("<button>").attr("type","button").attr("data-speed","1").addClass("speed on").html("x1.0"));
					$("#ModuleLmsInfoBar").append($("<button>").attr("type","button").attr("data-speed","1.25").addClass("speed").html("x1.2"));
					$("#ModuleLmsInfoBar").append($("<button>").attr("type","button").attr("data-speed","1.5").addClass("speed").html("x1.5"));
					$("#ModuleLmsInfoBar").append($("<button>").attr("type","button").attr("data-speed","2").addClass("speed").html("x2.0"));
					
					$("#ModuleLmsInfoBar button.speed").on("click",function() {
						Lms.youtube.player.setPlaybackRate($(this).attr("data-speed"));
					});
					
					var fullScreen = $("<button>").addClass("fullScreen").attr("onclick","Lms.youtube.fullScreen(true);").html('<i class="fa fa-expand"></i> 전체화면');
					$("#ModuleLmsInfoBar").append(fullScreen);
					
					/*
					e.target.seekTo(Youtube.YOUTUBE_LAST_POSITION);
					Youtube.YOUTUBE_PLAYED_PERCENTAGE = Youtube.viewTrackingBar();
					if (Youtube.PLAYING_CHECK == true) {
						if (Youtube.YOUTUBE_PLAYED_PERCENTAGE > 90) {
							$("#Player > .label").removeClass("label-primary label-danger").addClass("label label-primary").html("진도처리완료 ("+Youtube.YOUTUBE_PLAYED_PERCENTAGE+"%)");
						} else {
							$("#Player > .label").removeClass("label-primary label-danger").addClass("label label-danger").html("진도처리중 ("+Youtube.YOUTUBE_PLAYED_PERCENTAGE+"%)");
						}
					}
					*/
				},
				"onStateChange":function(e) {
					if (e.data == YT.PlayerState.PLAYING) {
						Lms.youtube.tracker("start");
						Lms.youtube.save("start");
						$("#ModuleLmsInfoBar button.play").hide();
						$("#ModuleLmsInfoBar button.pause").show();
						Lms.youtube.lastAfkTimer = Math.floor(Lms.youtube.player.getCurrentTime());
					}
					
					if (e.data == YT.PlayerState.PAUSED) {
						Lms.youtube.tracker("stop");
						Lms.youtube.save("stop");
						$("#ModuleLmsInfoBar button.play").show();
						$("#ModuleLmsInfoBar button.pause").hide();
					}
					
					if (e.data == YT.PlayerState.ENDED) {
						Lms.youtube.save("stop");
					}
					/*
					if (e.data == YT.PlayerState.PAUSED && Youtube.PLAYFORWARD_ERROR == true) {
						Youtube.PLAYFORWARD_ERROR = false;
						alert("진도처리중에는 앞으로 감을 수 없습니다.");
						Youtube.YOUTUBE_PLAYER_OBJECT.seekTo(Youtube.YOUTUBE_LAST_POSITION);
						Youtube.YOUTUBE_PLAYER_OBJECT.playVideo();
					}
					*/
					/*
					if (e.data == YT.PlayerState.ENDED) {
						Youtube.fullscreen(false);
						
						var ENDEDLAYER = $("<div>").addClass("end").width($("#Player").width()).height($("#Player").height()).css("lineHeight",$("#Player").height()+"px").html("재생이 완료되었습니다. 이곳을 클릭하면 다시 볼 수 있습니다.");
						ENDEDLAYER.on("click",function() {
							$(this).remove();
							Youtube.YOUTUBE_PLAYER_OBJECT.seekTo(0);
							Youtube.YOUTUBE_PLAYER_OBJECT.playVideo();
						});
						$("#Player").append(ENDEDLAYER);
						
						// Check User Tracking
						Youtube.checkUserTracking(Math.floor(Youtube.YOUTUBE_PLAYER_OBJECT.getDuration() - 1));
						Youtube.checkUserTracking(Math.floor(Youtube.YOUTUBE_PLAYER_OBJECT.getDuration()));
		
						// View Tracking Bar
						Youtube.YOUTUBE_PLAYED_PERCENTAGE = Youtube.viewTrackingBar();
						if (Youtube.PLAYING_CHECK == true) {
							if (Youtube.YOUTUBE_PLAYED_PERCENTAGE > 90) {
								$("#Player > .label").removeClass("label-primary label-danger").addClass("label label-primary").html("진도처리완료 ("+Youtube.YOUTUBE_PLAYED_PERCENTAGE+"%)");
							} else {
								$("#Player > .label").removeClass("label-primary label-danger").addClass("label label-danger").html("진도처리중 ("+Youtube.YOUTUBE_PLAYED_PERCENTAGE+"%)");
							}
						} else {
							$("#Player > .label").html("진도처리하지않음 ("+Youtube.YOUTUBE_PLAYED_PERCENTAGE+"%)");
						}
						
						Youtube.saveData(true);
					}
					
					Youtube.saveData();
					
					if (e.data == YT.PlayerState.ENDED) { // Ended
						YOUTUBE_PLAYED_DURATION[YOUTUBE_PLAYED_DURATION.length - 1] = true;
						CheckTimer();
					}
					*/
				},
				"onPlaybackRateChange":function(e) {
					$("#ModuleLmsInfoBar button.speed").removeClass("on");
					$("#ModuleLmsInfoBar button.speed[data-speed='"+e.data+"']").addClass("on");
				}
			}
		});
		Lms.youtube.player.loaded = false;
	},
	getTrackerAverage:function(position,barCount,tracking) {
		var barCount = barCount ? barCount : Lms.youtube.trackerBarCount;
		var time = tracking ? tracking.length : Lms.youtube.trackerTotalTime;
		
		var startPosition = position == 0 ? 0 : position - 1;
		var EndPosition = position == barCount ? barCount - 1 : position;
		
		var startTime = Math.round(time / barCount * startPosition);
		var endTime = Math.round(time / barCount * EndPosition);
		
		var sum = 0;
		var total = 0;
		for (var i=startTime;i<=endTime;i++) {
			if (tracking) {
				sum+= tracking[i] == "z" ? 32 : parseInt(tracking[i],32);
			} else {
				if (Lms.youtube.trackerType == "P") sum+= Lms.youtube.tracking.tracking[i];
				else sum+= Lms.youtube.tracking.tracking[i] == "z" ? 32 : parseInt(Lms.youtube.tracking.tracking[i],32);
			}
			total++;
		}
		
		return Math.round(sum / total);
	},
	animateStatusBar:function(position,type) {
		var statusBar = $("#ModuleLmsStatusBar > div"); // $("#ModuleLmsYoutubePlayerTrackerStatus");
		
		var count = $(statusBar[position]).data("count");
		if (type == "none") {
			$(statusBar[position]).find("div.bar").height(Math.round(count / Lms.youtube.trackerStatusMax * 65));
		} else {
			$(statusBar[position]).find("div.bar").animate({height:Math.round(count / Lms.youtube.trackerStatusMax * 65)},type);
		}
	},
	drawStatus:function(position) {
		var statusBar = $("#ModuleLmsStatusBar");
		if (statusBar.is(":visible") == false) return;
		
		if (position == "init" || position == "resize") {
			Lms.youtube.trackerStatusMax = Lms.youtube.trackerType == "P" ? Lms.youtube.tracking.tracking.slice().sort(function(left,right) { return right - left; }).shift() : parseInt(Lms.youtube.tracking.tracking.slice().sort(function(left,right) { return right - left; }).shift(),32);
			statusBar.empty();
			for (var i=0;i<Lms.youtube.trackerBarCount;i++) {
				var count = Lms.youtube.getTrackerAverage(i);
				var status = $("<div>");
				status.data("count",count).data("position",i);
				status.append($("<div>").addClass("bar").height(0));
				status.on("mouseover",function() {
					$(this).addClass("over");
					$(this).append($("<div>").addClass("view").html($(this).data("count")+"회 시청함"));
				});
				status.on("mouseout",function() {
					$(this).removeClass("over");
					$(this).find("div.view").remove();
				});
				status.on("click",function() {
					var time = Math.round(Lms.youtube.trackerTotalTime / Lms.youtube.trackerBarCount * $(this).data("position"));
					Lms.youtube.player.seekTo(time);
				});
				statusBar.append(status);
				$($("#ModuleLmsStatusBar > div")[i]).data("count",count);
			}
			
			if (position == "init") {
				for (var i=0;i<Lms.youtube.trackerBarCount;i++) {
					setTimeout(Lms.youtube.animateStatusBar,500+i*20,i,"fast");
				}
			} else {
				for (var i=0;i<Lms.youtube.trackerBarCount;i++) {
					Lms.youtube.animateStatusBar(i,"none");
				}
			}
			
			if (Lms.youtube.player == null || Lms.youtube.player.loaded !== true) {
				var position = Math.round(Lms.youtube.player.getCurrentTime() / Lms.youtube.trackerTotalTime * Lms.youtube.trackerBarCount);
				statusBar.find("div.prev").removeClass("prev");
				for (var i=0;i<=position;i++) {
					$($("#ModuleLmsStatusBar > div")[i]).addClass("prev");
				}
			}
		} else if (position == "update") {
			if (Lms.youtube.trackerType == "P") return;
			Lms.youtube.trackerStatusMax = Lms.youtube.tracking.tracking.slice().sort().pop();
			
			for (var i=0;i<Lms.youtube.trackerBarCount;i++) {
				setTimeout(Lms.youtube.animateStatusBar,i*20,i,"fast");
			}
		} else {
			if (Lms.youtube.trackerType == "P") return;
			var count = Lms.youtube.getTrackerAverage(position);
			$($("#ModuleLmsStatusBar > div")[position]).data("count",count);
			if (Lms.youtube.trackerStatusMax < count) {
				Lms.youtube.drawStatus("update");
			} else {
				Lms.youtube.animateStatusBar(position,"slow");
			}
		}
	},
	drawTracker:function(position) {
//		var statusBar = $("#ModuleLmsStatusBar"); // $("#ModuleLmsYoutubePlayerTrackerStatus");
		var trackingBar = $("#ModuleLmsTrackingBar"); // $("#ModuleLmsYoutubePlayerTrackerBar");
		
		if (position == "init" || position == "resize") {
			Lms.youtube.trackerStatusMax = Lms.youtube.tracking.tracking.slice().sort(function(left,right) { return right - left; }).shift();
//			statusBar.empty();
			trackingBar.empty();
			Lms.youtube.trackerBarCount = Math.floor(trackingBar.width() / 4) + 1;
			Lms.youtube.trackerBarInterval = Lms.youtube.trackerTotalTime / 4;
			for (var i=0;i<Lms.youtube.trackerBarCount;i++) {
				var count = Lms.youtube.getTrackerAverage(i);
				trackingBar.append(count > 0 ? $("<div>").addClass("on") : $("<div>"));
				/*
				var status = $("<div>");
				status.data("count",count).data("position",i);
				status.append($("<div>").addClass("bar").height(0));
				status.on("mouseover",function() {
					$(this).addClass("over");
					$(this).append($("<div>").addClass("view").html($(this).data("count")+"회 시청함"));
				});
				status.on("mouseout",function() {
					$(this).removeClass("over");
					$(this).find("div.view").remove();
				});
				status.on("click",function() {
					var time = Math.round(Lms.youtube.trackerTotalTime / Lms.youtube.trackerBarCount * $(this).data("position"));
					Lms.youtube.player.seekTo(time);
				});
				statusBar.append(status);
				$(statusBar.find("div")[i]).data("count",count);
				*/
			}
			/*
			if (position == "init") {
				for (var i=0;i<Lms.youtube.trackerBarCount;i++) {
					setTimeout(Lms.youtube.animateStatusBar,500+i*10,i,"fast");
				}
			} else {
				for (var i=0;i<Lms.youtube.trackerBarCount;i++) {
					Lms.youtube.animateStatusBar(i,"none");
				}
			}
			
			if (Lms.youtube.player == null || Lms.youtube.player.loaded !== true) {
				var position = Math.round(Lms.youtube.player.getCurrentTime() / Lms.youtube.trackerTotalTime * Lms.youtube.trackerBarCount);
				statusBar.find("div.prev").removeClass("prev");
				for (var i=0;i<=position;i++) {
					$(statusBar.find("div")[i]).addClass("prev");
				}
			}
			*/
		} /*else if (position == "update") {
			if (Lms.youtube.trackerType == "P") return;
			Lms.youtube.trackerStatusMax = Lms.youtube.tracking.tracking.slice().sort().pop();
			
			for (var i=0;i<Lms.youtube.trackerBarCount;i++) {
				setTimeout(Lms.youtube.animateStatusBar,i*20,i,"fast");
			}
		} */else {
			if (Lms.youtube.trackerType == "P") return;
			var count = Lms.youtube.getTrackerAverage(position);
			if (count > 0) {
				$(trackingBar.find("div")[position]).addClass("on");
			}
			/*
			$(statusBar.find("div")[position]).data("count",count);
			if (Lms.youtube.trackerStatusMax < count) {
				Lms.youtube.drawTracker("update");
			} else {
//				$($("#ModuleLmsYoutubePlayerTrackerStatus > div")[position]).find("div").height((count / Lms.youtube.trackerStatusMax * 100)+"%");
				Lms.youtube.animateStatusBar(position,"slow");
			}
			*/
		}
	},
	updateTracker:function(time,force) {
		$("#ModuleLmsPositionBar").css("width",(time / Lms.youtube.trackerTotalTime * 100)+"%");
		$("#ModuleLmsPostionPin").css("left",(time / Lms.youtube.trackerTotalTime * 100)+"%");
		var statusBar = $("#ModuleLmsStatusBar > div"); // $("#ModuleLmsYoutubePlayerTrackerStatus");
		var trackingBar = $("#ModuleLmsTrackerBar > div"); // $("#ModuleLmsYoutubePlayerTrackerBar");
		
		var position = Math.round(time / Lms.youtube.trackerTotalTime * Lms.youtube.trackerBarCount);
		if (Lms.youtube.lastCheckPosition == position) return;
		
		if (Lms.youtube.lastCheckPosition > position) {
			$("#ModuleLmsStatusBar > div.prev").removeClass("prev");
			for (var i=0;i<=position;i++) {
				$(statusBar[i]).addClass("prev");
			}
		} else {
			for (var i=Lms.youtube.lastCheckPosition + 1;i<=position;i++) {
				$(statusBar[i]).addClass("prev");
			}
		}
		
		Lms.youtube.drawTracker(position);
		Lms.youtube.drawStatus(position);
		Lms.youtube.lastCheckPosition = position;
	},
	increaseCount:function(time) {
		if (Lms.youtube.trackerType == "P") return;
		if (Lms.youtube.tracking.tracking[time] == "v") Lms.youtube.tracking.tracking[time] = "z";
		if (Lms.youtube.tracking.tracking[time] == "z") return;
		Lms.youtube.tracking.tracking[time] = (parseInt(Lms.youtube.tracking.tracking[time],32) + 1).toString(32);
	},
	tracker:function(mode) {
		if (mode == "start") {
			if (Lms.youtube.trackerTimer != null) {
				clearInterval(Lms.youtube.trackerTimer);
				Lms.youtube.trackerTimer = null;
			}
			Lms.youtube.trackerTimer = setInterval(Lms.youtube.tracker,500,"interval");
		}
		
		if (mode == "stop") {
			if (Lms.youtube.trackerTimer != null) {
				clearInterval(Lms.youtube.trackerTimer);
				Lms.youtube.trackerTimer = null;
			}
		}
		
		if (Lms.youtube.player == null || Lms.youtube.player.loaded !== true) return;
		
		var time = Math.ceil(Lms.youtube.player.getCurrentTime()) - 1;
		$("#ModuleLmsInfoBar .timer > .current").html(Lms.youtube.getTime(Math.round(Lms.youtube.player.getCurrentTime())));
		
		if (Lms.youtube.lastCheckTime != time) {
			if (Lms.youtube.lastCheckTime > time || Lms.youtube.lastCheckTime + 5 < time) { // playback or jump
				Lms.youtube.increaseCount(time);
				Lms.youtube.updateTracker(time);
			} else {
				for (i=Lms.youtube.lastCheckTime + 1;i<=time;i++) {
					Lms.youtube.increaseCount(i);
					Lms.youtube.updateTracker(i);
				}
			}
		}
		
		if (Lms.youtube.context.afk_check == true && Lms.youtube.lastAfkTimer + Lms.youtube.context.afk_check_time < time) {
			Lms.youtube.afkChecker("start");
		}
		
		Lms.youtube.lastCheckTime = time;
	},
	save:function(mode) {
		if (Lms.youtube.trackerType == "P") return;
		
		if (mode == "start") {
			if (Lms.youtube.saveTimer != null) {
				clearInterval(Lms.youtube.saveTimer);
				Lms.youtube.saveTimer = null;
			}
			
			Lms.youtube.saveTimer = setInterval(Lms.youtube.save,10000,"interval");
			return;
		}
		
		if (mode == "stop") {
			if (Lms.youtube.saveTimer != null) {
				clearInterval(Lms.youtube.saveTimer);
				Lms.youtube.saveTimer = null;
			}
		}
		
		if (mode == "interval" && Lms.youtube.saving == true) return;
		
		$.ajax({
			type:"POST",
			url:ENV.getProcessUrl("lms","tracking"),
			data:{pidx:Lms.youtube.pidx,tracking:"T"+Lms.youtube.tracking.tracking.join(""),last_position:Lms.youtube.lastCheckTime},
			dataType:"json",
			success:function(result) {
				Lms.youtube.saving = false;
			},
			error:function() {
				Lms.youtube.saving = false;
			}
		});
	},
	fullScreen:function(isFull) {
		if (Lms.youtube.player == null || Lms.youtube.player.loaded !== true) return;
		
		Lms.youtube.player.pauseVideo();
		var player = $("#ModuleLmsYoutubePlayer");
		var element = player.get(0);
		
		if (isFull == true) {
			
//			$player.attr("data-width",player.width()).attr("data-height",$("#Player").height());
//			player.addClass("fullScreen");//width("100%").height("100%").css("paddingBottom",0).css("marginBottom",0);
			
			if (element.requestFullscreen) {
			  element.requestFullscreen();
			} else if (element.msRequestFullscreen) {
			  element.msRequestFullscreen();
			} else if (element.mozRequestFullScreen) {
			  element.mozRequestFullScreen();
			} else if (element.webkitRequestFullscreen) {
			  element.webkitRequestFullscreen();
			}
		} else {
			if (window.document.webkitExitFullscreen) {
				document.webkitExitFullscreen();
			} else if (window.document.mozExitFullscreen) {
				document.mozExitFullscreen();
			} else if (window.msExitFullscreen) {
				document.msExitFullscreen();
			}
		}
	},
	afkChecker:function(mode) {
		if (Lms.youtube.player == null || Lms.youtube.player.loaded !== true) return;
		if (Lms.youtube.trackerType == "P") return;
		var button = $("#ModuleLmsYoutubePlayer .btnAfkCheck");
		if (mode == "start") {
			if (button.length == 0) {
				if (Lms.youtube.afkTimer != null) {
					clearInterval(Lms.youtube.afkTimer);
					Lms.youtube.afkTimer = null;
				}
				
				$("#ModuleLmsYoutubePlayer").append($("<div>").addClass("cover"));
				
				var button = $("<div>").addClass("btn btnRed btnAfkCheck");
				button.data("count",10);
				$("#ModuleLmsYoutubePlayer").append(button);
			
				button.html("자리비움확인중 : 10초 이내 마우스를 움직여주세요.");
				Lms.youtube.afkTimer = setInterval(Lms.youtube.afkChecker,1000,"inverval");
			}
		}
		
		if (mode == "inverval") {
			if (button.length == 0) return;
			
			button.data("count",button.data("count") - 1);
			button.html("자리비움확인중 : "+button.data("count")+"초 이내 마우스를 움직여주세요.");
			
			if (button.data("count") == 0) {
				Lms.youtube.player.pauseVideo();
				button.html("다시 재생을 시작하려면 이곳을 클릭하여 주십시오.");
				$("#ModuleLmsYoutubePlayer .cover").remove();
				$("#ModuleLmsYoutubePlayer").append($("<div>").addClass("disabled"));
				button.on("click",function() {
					$(this).remove();
					$("#ModuleLmsYoutubePlayer .disabled").remove();
					Lms.youtube.player.playVideo();
				});
				
				clearInterval(Lms.youtube.afkTimer);
				Lms.youtube.afkTimer = null;
			}
		}
		
		if (mode == "stop") {
			Lms.youtube.lastAfkTimer = Math.floor(Lms.youtube.player.getCurrentTime());
			
			if (Lms.youtube.afkTimer != null) {
				clearInterval(Lms.youtube.afkTimer);
				Lms.youtube.afkTimer = null;
			}
			
			if (button.length == 0) return;
			if (button.data("count") > 0) {
				$("#ModuleLmsYoutubePlayer .cover").remove();
				$("#ModuleLmsYoutubePlayer .btnAfkCheck").remove();
			}
		}
	},
	drawTrackingList:function() {
		var items = $("#ModuleLmsTrackingList div.item, #ModuleLmsPlayingStatus div.item");
		
		for (var i=0, loop=items.length;i<loop;i++) {
			var item = $(items[i]);
			var graph = $(items[i]).find(".graph");
			
			var trackerCount = Math.floor(graph.width() / 3) + 1;
			var trackerBar = $("<div>").addClass("trackerBar");
			var trackerStatus = $("<div>").addClass("trackerStatus");
			
			var maxCount = parseInt(item.attr("data-tracking").split("").sort(function(left,right) { return right - left; }).shift(),32);
			console.log(item,maxCount);
			
			for (var j=0;j<trackerCount;j++) {
				var count = Lms.youtube.getTrackerAverage(j,trackerCount,item.attr("data-tracking"));
				trackerBar.append(count > 0 ? $("<div>").addClass("on") : $("<div>"));
				
				var status = $("<div>").data("count",count).data("position",i);
				status.append($("<div>").addClass("bar").height(Math.round(count / maxCount * 40)));
				
				status.on("mouseover",function() {
					$(this).addClass("over");
					$(this).append($("<div>").addClass("view").html($(this).data("count")+"회 시청함"));
				});
				status.on("mouseout",function() {
					$(this).removeClass("over");
					$(this).find("div.view").remove();
				});
				status.on("click",function() {
					var time = Math.round(Lms.youtube.trackerTotalTime / trackerCount * $(this).data("position"));
					console.log(Lms.youtube.trackerTotalTime, trackerCount, $(this).data("position"));
					Lms.youtube.player.seekTo(time);
				});
				
				trackerStatus.append(status);
			}
			
			graph.append(trackerStatus);
			graph.append(trackerBar);
		}
	},
	getPosition:function() {
		if (Lms.youtube.player == null || Lms.youtube.player.loaded !== true) return 0;
		return Lms.youtube.player.getCurrentTime() / Lms.youtube.player.getDuration() * 100;
	},
	setPosition:function(percent) {
		if (Lms.youtube.player == null || Lms.youtube.player.loaded !== true) {
			$("#ModuleLmsPostionPin").css("left",0);
			$("#ModuleLmsPositionBar").css("width",0);
		} else {
			var time = Lms.youtube.player.getDuration() * percent / 100;
			console.log(Lms.youtube.player.getDuration(),percent);
			Lms.youtube.player.seekTo(time);
		}
	}
};

function onYouTubeIframeAPIReady() {
	Lms.youtube.initPlayer();
//	setInterval(Youtube.saveData,10000);

}

$(window).on("mousemove",function() {
	Lms.youtube.afkChecker("stop");
});


$(document).on("webkitfullscreenchange mozfullscreenchange fullscreenchange",function(e) {
	$("#ModuleLmsStatusBar").hide();
	$("#ModuleLmsInfoBar button.statusToggle").removeClass("off");
	var viewBar = $("#ModuleLmsViewBar").clone();
	$("#ModuleLmsViewBar").remove();
	
	if ((document.webkitIsFullScreen !== undefined && document.webkitIsFullScreen === true) || (document.mozFullScreen !== undefined && document.mozFullScreen === true) || (document.msFullscreenElement !== undefined && document.msFullscreenElement !== null)) {
		$("#ModuleLmsYoutubePlayer").addClass("fullScreen");
		
		$("#ModuleLmsYoutubePlayer").append(viewBar);
		Lms.youtube.drawTracker("resize");
		
		var close = $("<div>").addClass("btn btnRed btnClose").html("<i class='fa fa-compress'></i> 전체화면종료");
		close.on("click",function() {
			Lms.youtube.fullScreen(false);
		});
		$("#ModuleLmsYoutubePlayer").append(close);
	} else {
		Lms.youtube.drawTracker("resize");
		$("#ModuleLmsYoutubePlayer").removeClass("fullScreen");
		$("#ModuleLmsYoutubePlayer .btnClose").remove();
		
		$("body").append(viewBar);
	}
	
	Lms.youtube.player.playVideo();
});

$(document).on("Lms.post.init",function(e,form) {
	if (form.find("input[name=afk_check]").is(":checked") == false) {
		form.find("input[name=afk_check_time]").parents(".inputBlock").hide();
	}
	
	form.find("input[name=url]").on("change",function(e) {
		form.find("input[name=id]").val("");
		form.find("input[name=thumbnail]").val("");
		form.find("input[name=time]").val("");
		form.find("input[name=status]").val("");
		form.find("input[name=caption]").val("");
		
		form.find("img.thumbnail").remove();
		
		var temp = $(this).val().split("v=");
		if (temp.length == 1) {
			iModule.inputStatus($(this),"error");
			return;
		}

		var id = temp.pop().split("&").shift();
		Lms.youtube.getVideoInfo(id,function(data) {
			if (data != null) {
				form.find("input[name=title]").val(data.snippet.title);
				var thumbnail = data.snippet.thumbnails.standard ? data.snippet.thumbnails.standard.url : data.snippet.thumbnails.high.url;
				form.find("input[name=title]").parents(".inputBlock").find(".helpBlock").append($("<img>").addClass("thumbnail").attr("src",thumbnail).css("width","100%").css("marginTop","5px"));
				form.find("input[name=id]").val(data.id);
				form.find("input[name=thumbnail]").val(thumbnail);
				form.find("input[name=caption]").val(data.contentDetails.caption);
				form.find("input[name=time]").val(data.contentDetails.duration);
				form.find("input[name=status]").val("serving");
			} else {
				iModule.inputStatus(form.find("input[name=url]"),"error");
			}
		});
	});
	
	form.find("input[name=afk_check]").on("change",function() {
		if ($(this).is(":checked") == true) {
			form.find("input[name=afk_check_time]").parents(".inputBlock").show();
		} else {
			form.find("input[name=afk_check_time]").parents(".inputBlock").hide();
		}
	});
	
	
});