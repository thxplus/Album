		function Waterfall(param){ 
  	  this.id = typeof param.container == 'string' ? document.getElementById(param.container) : param.container;
  	  this.colWidth = param.colWidth;
  	  this.colCount = param.colCount;
  	  this.cls = param.cls && param.cls != '' ? param.cls : 'photo';
  	  this.init();
		}
		
		Waterfall.prototype = {
		    getByClass:function(cls,p){
		        var arr = [],reg = new RegExp("(^|\\s+)" + cls + "(\\s+|$)","g");
		        var nodes = p.getElementsByTagName("*"),len = nodes.length;
		        for(var i = 0; i < len; i++){
		            if(reg.test(nodes[i].className)){
		                arr.push(nodes[i]);
		                reg.lastIndex = 0;
		            }
		        }
		        return arr;
		    },
		    maxArr:function(arr){
		        var len = arr.length,temp = arr[0];
		        for(var ii= 1; ii < len; ii++){
		            if(temp < arr[ii]){
		                temp = arr[ii];
		            }
		        }
		        return temp;
		    },
		    getMar:function(node){
		        var dis = 0;
		        if(node.currentStyle){
		            dis = parseInt(node.currentStyle.marginBottom);
		        }else if(document.defaultView){
		            dis = parseInt(document.defaultView.getComputedStyle(node,null).marginBottom);
		        }
		        return dis;
		    },
			getMinCol:function(arr){
				var ca = arr,cl = ca.length,temp = ca[0],minc = 0;
				for(var ci = 0; ci < cl; ci++){
					if(temp > ca[ci]){
						temp = ca[ci];
						minc = ci;
					}
				}
				return minc;
			},
		    init:function(){
		        var _this = this;
		        var col = [],//列高
				    iArr = [];//索引
		        var nodes = _this.getByClass(_this.cls,_this.id),len = nodes.length;
		        for(var i = 0; i < _this.colCount; i++){
		            col[i] = 0;
		        }
		        for(var i = 0; i < len; i++){
		            nodes[i].h = nodes[i].offsetHeight + _this.getMar(nodes[i]);
		            iArr[i] = i;
		        }
		
				for(var i = 0; i < len; i++){
					var ming = _this.getMinCol(col);
					nodes[i].style.left = ming * _this.colWidth + "px";
					nodes[i].style.top = col[ming] + "px";
					col[ming] += nodes[i].h;
				}
		
				_this.id.style.height = _this.maxArr(col) + "px";
		    }
		};
			
		function imgshow( waitFor ) {
			this.waitFor = waitFor;
			this.div = null;
			this.image = null;
		
			this.showInterval = 0;
			this.waitInterval = 0;
			this.visible = false;
		
			this.waitInitialize = function() {
					clearInterval( this.waitInterval );
					this.initialize();
			}
		
			this.initialize = function() {
				var that = this;
				
				this.div = document.createElement( 'div' );
				this.div.id = 'photoshow';
				document.body.appendChild( this.div );
				
				this.div_sidebar = document.createElement( 'div' );
				this.div_sidebar.id = 'photoshow_sidebar';
				this.div.appendChild( this.div_sidebar );
				
				this.div_close = document.createElement( 'div' );
				this.div_close.id = 'photoshow_close';
				this.div_close.ClassName = 'photoshow_close';
				this.div.appendChild( this.div_close );
				
				//var anchors = document.getElementsByClassName('photourl');
				var anchors = document.getElementsByTagName('a');
				for( var i=0; i<anchors.length; i++ ) {
					var a = anchors[i];	
					if ( a.className == 'photourl' ){
						a.onclick = function(e) { e.preventDefault();return that.show(this); }
					}
				}
		
			}
			this.showCallback = function() {
				if( this.image && (this.image.width > 0 || this.image.complete) ) {
					//this.div.style.display = 'none';
					if( this.image.width > 0 ) {
						clearInterval( this.showInterval );
						var yScrolls;
						if (self.pageYOffset) {
							yScrolls = self.pageYOffset;
						} else if (document.documentElement && document.documentElement.scrollTop){	
							yScrolls = document.documentElement.scrollTop;
						} else if (document.body) {
							yScrolls = document.body.scrollTop;
						}
						
						var wh = this.image.width / this.image.height ;
						
						if ( this.image.height > document.documentElement.clientHeight ){
							this.image.height = document.documentElement.clientHeight - 80 ;
							this.image.width = this.image.height * wh ;
						}
						
						if ( this.image.width > ( document.documentElement.clientWidth - 220 ) ){
							this.image.width = document.documentElement.clientWidth - 220 ;
							this.image.height = this.image.width / wh ;
						}
						
						this.div.style.top = Math.max( yScrolls + (document.documentElement.clientHeight - this.image.height + 40  )/2, 0) + "px";
						this.div.style.left = Math.max((document.documentElement.clientWidth - this.image.width - 4 - 200 )/2, 0 ) + "px";
						this.div.style.width = this.image.width + 206 + 'px';
						this.div.style.height = this.image.height + 6 + 'px';
						
						if ( this.image.height < 400 ){
							var h = 400 ;
							this.div.style.cssText = 'background-position-x:' + ( h * wh ) / 2 + 'px;line-height:' + h + 'px;';
							this.div.style.height = h + 'px';
							this.div.style.width = h * wh + 200 + 'px';
							this.div.style.top = Math.max( yScrolls + (document.documentElement.clientHeight - h  )/2, 0) + "px";
							this.div.style.left = Math.max((document.documentElement.clientWidth - h * wh - 200 )/2, 0 ) + "px";
							
						}
						
						this.div.style.display = 'block';
					}
						
				}
			}
			this.show = function( anchor ) {
				if( this.visible ) {
					this.hide();
					return false;
				}

				this.image = new Image();
				this.image.src = anchor.href ;
				//this.image.title = "";
				this.image.onclick = function() { return that.hide(); }
				this.div_close.onclick = function() { return that.hide(); }
				this.div.appendChild( this.image );
				
				var info = eval('(' + anchor.id + ')');
				
				var info_op = '<div>尺寸<span>' + info.size + '</span></div>' +
											'<div>宽度<span>' + info.width + 'px</span></div>' +
											'<div>高度<span>' + info.height + 'px</span></div>' ;
										
				
				
				if ( info.camera ){
					var info_op = '<div>相机<span>' + info.camera + '</span></div>' + 
												'<div>镜头<span>' + info.lens + '</span></div>' + 
												'<div>光圈<span>' + info.aperture + '</span></div>' + 
												'<div>快门<span>' + info.shutterSpeed + '</span></div>' + 
												'<div>焦距<span>' + info.focalLength + 'mm</span></div>' + 
												'<div>感光<span>' + info.ISO + '</span></div>' + 
												'<div>时间<span>' + info.time + '</span></div>' +
												info_op ;
														
				}
				
				if ( info.url ){
					var info_op = '<div>来源<span><a href="' + info.url + '" target="_bank">查看文章</a></span></div>' +
												info_op ;
				}
				
				if ( info.description ){
					var info_op = '<div>来源<span><a href="http://' + info.description + '" target="_bank">' + info.description + '</a></span></div>' +
												info_op ;
				}
				
				this.div_sidebar.innerHTML = 	'<div class="div_sidebar_title">图片描述</div>' + '<div class="div_sidebar_content">' + anchor.title + '</div>' +
																			'<div class="div_sidebar_title">图片信息</div>' + '<div class="div_sidebar_content">' + info_op + '</div>' ; 
				this.div_close.innerHTML = 	'关闭图片';
				
				this.div.style.cssText = 'background-position-x:200px';
				this.div.style.width = '600px';
				this.div.style.height = '400px';
				
				var yScroll;
				if (self.pageYOffset) {
					yScroll = self.pageYOffset;
				} else if (document.documentElement && document.documentElement.scrollTop){
					yScroll = document.documentElement.scrollTop;
				} else if (document.body) {
					yScroll = document.body.scrollTop;
				}
				
				this.div.style.top = Math.max( yScroll + (document.documentElement.clientHeight - 404 )/2, 0) + "px";
				this.div.style.left = Math.max((document.documentElement.clientWidth - 604 )/2, 0 ) + "px";
				this.div.style.display = 'block';
				this.showInterval = setInterval( function() { that.showCallback() }, 10 );
				this.visible = true;
				return false;
			}
		
			this.hide = function() {
				this.div.style.display = 'none';
				this.div.removeChild( this.image );
				this.image = null;
				this.visible = false;
			}
		
			var that = this;
			this.waitInterval = setInterval( function() { that.waitInitialize() }, 100 );
		}