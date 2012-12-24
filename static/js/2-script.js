
/**
 * Gettext function
 *
 * @param string name	Name of the variable
 * @param array params	Associative array of strings to be replaced in the text
 * @return string	Value of the variable
 */
function __(name, params){
    if(Translations[name]){
        if(typeof(params) == "object"){
            var text = Translations[name];
            for(key in params)
                text = text.replace('{'+key+'}', params[key]);
            return text;
        }else{
            return Translations[name];
        }
    }
    else{
        return name;
    }
}

Locale.use('fr-FR');


Element.implement({
    // Textarea auto-resizing
    resizable : function(){
        var t = this,
        resize = function(){
            var lines = t.value.replace(/[^\n]/, "").length;
            if(t.lines > lines){
                t.style.height = "1px";
            }
            t.lines = lines;
				
            var sh = Math.max(t.scrollHeight, t.defaultSize);
            if(t.offsetHeight < sh)
                t.style.height = (sh+5)+"px";
            return t;
        };
        if(t.retrieve("resizable"))
            return;
        t	.setStyles({
            overflow : "hidden",
            resize : "none"
        })
        .store("resizable", true)
        .addEvent("focus", resize)
        .addEvent("keyup", resize)
        .addEvent("keypress", resize);
        t.lines = t.value.replace(/[^\n]/, "").length;
        t.defaultSize = t.getStyle("height").toInt();
        return this;
    }
});

String.implement({
    // Deletes spaces before and after the string
    trim : function() {
        return this.replace(/(^\s+|\s+$)/g, "");
    },
	
    // Convert special characters to HTML entities
    htmlspecialchars : function(){
        return this
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;");
    }
});




/* Publication form */

var Post = {
	
    pageOfficial : 1,
    pageNonOfficial : 1,
    busy : false,
	
    currentPhoto : -1,
	
    init : function(){
        if($("publish-message"))
            this.initForm();
        if($("attachment-photo"))
            this.initPhoto();
        this.initDelete();
		
        $$(".posts-more-link").each(function(link){
            var url_more = link.get("href"),
            is_official = link.hasClass("official");
            link.set("href", "javascript:;")
            .addEvent("click", function(){
                if(Post.busy)
                    return;
                Post.busy = true;
                var page = Post[is_official ? "pageOfficial" : "pageNonOfficial"] + 1;
                new Request({
                    url: url_more.replace("{page}", page),
                    onSuccess: function(data){
                        var el = new Element("div", {
                            html: data
                        }).inject(link, "before");
                        if(el.get("html").trim() == ""){
                            link.tween("opacity", 0);
                        }else{
                            Post.initDelete(el);
                            Comment.init(el.getElements(".post-comments"));
                            Survey.init(el.getElements(".survey"));
                            Slimbox.scan(el);
                        }
                        Post[is_official ? "pageOfficial" : "pageNonOfficial"]++;
                        Post.busy = false;
                    }
                }).post();
            });
        });
		
    },
	
	
    // Viewing a photo gallery
    initPhoto : function(){
        window.addEvent('hashchange', function(hash){
            var photos = $$('.photos');
            if(photos.length == 0)
                return;
            var m = hash.match(/^photo-([0-9]+)$/);
            if(m){
                photos[0].addClass('hidden');
                $("attachment-photo").removeClass('hidden');
				
                var i = -1;
                for(var j=0; j < Post.photos.length; j++){
                    if(Post.photos[j].id == m[1]){
                        i = j;
                        break;
                    }
                }
                if(i == -1){
                    location.hash = '';
                }else{
                    Post.currentPhoto = i;
                    var photo = Post.photos[i],
                    img = $("attachment-photo-img");
                    if(img)
                        img.set("src", photo.url);
                    else
                        new Element("img", {
                            "id" : "attachment-photo-img",
                            "src" : photo.url
                        })
                        .addEvent("click", function(){
                            $("attachment-photo-next").fireEvent("click");
                        })
                        .inject($("attachment-photo"));
                    // Cas de base : On a jamais aimé    
                    $$('.like-link').removeClass('hidden');
                    $$('.unlike-link').addClass('hidden');
                    // Pour chaques Likes :
                    $$(".post-like").each(function(l){
                        // Cas ou quelqu'un a deja aimé && c'est la photo affiché.
                        if(l.hasClass("post-like-attachment-"+photo.id)){
                            // On affiche la "Like Box"
                            l.removeClass("hidden");
                            // Cas ou on a personnellement aimé
                            if($('like-it-'+photo.id) === null){
                                // On affiche "Je n'aime plus !"
                                $$('.like-link').removeClass('hidden');
                                $$('.unlike-link').addClass('hidden');
                            } else {
                                // On affiche "J'aime"
                                $$('.like-link').addClass('hidden');
                                $$('.unlike-link').removeClass('hidden');
                            }
                        } else {
                            // C'est pas la bonne photo, on cache la "Like Box"
                            l.addClass("hidden");
                        }
                    });
                    $$(".post-comment").each(function(e){
                        if(e.hasClass("post-comment-attachment"+photo.id))
                            e.removeClass("hidden");
                        else
                            e.addClass("hidden");
                    });
                    $$(".post-delete").addClass("hidden");
                }
				
            }else if(photos[0].hasClass('hidden')){
                photos[0].removeClass('hidden');
                $("attachment-photo").addClass('hidden');
				
                Post.currentPhoto = -1;
				$$(".post-like").each(function(l){
                        if(l.hasClass("post-like-attachment-0"))
                            l.removeClass("hidden");
                        else
                            l.addClass("hidden");
                });		
                $$(".post-comment").each(function(e){
                    if(e.hasClass("post-comment-attachment0"))
                        e.removeClass("hidden");
                    else
                        e.addClass("hidden");
                });
            }
        });
        if(location.hash.indexOf('#') == 0)
            window.fireEvent('hashchange', location.hash.substr(1));
		
        var prev = function(){
            var i = Post.currentPhoto-1;
            if(i < 0)
                i = Post.photos.length-1;
            location.hash = '#photo-'+Post.photos[i].id;
        };
        var next = function(){
            var i = Post.currentPhoto+1;
            if(i >= Post.photos.length)
                i = 0;
            location.hash = '#photo-'+Post.photos[i].id;
        };
        $("attachment-photo-prev").addEvent("click", prev);
        $("attachment-photo-next").addEvent("click", next);
        $("attachment-photo-album").addEvent("click", function(){
            location.hash = '';
        });
        window.addEvent("keydown", function(e){
            if(e.target && ["INPUT", "SELECT", "TEXTAREA"].contains(e.target.tagName))
                return;
            if(e.key == 'right' || e.key == 'space'){
                next();
                return false;
            }
            if(e.key == 'left' || e.key == 'backspace'){
                prev();
                return false;
            }
            if(e.key == 'up'){
                location.hash = '';
                return false;
            }
        });
    },
	
    initDelete : function(el){
        if(el == null)
            el = $$(".post");
        if(typeOf(el) == "elements"){
            el.each(function(e){
                Post.initDelete(e);
            });
            return;
        }
        if(!el.hasClass("post")){
            Post.initDelete(el.getElements(".post"));
            return;
        }
        var d = el.getElements(".post-delete")[0];
        if(!d || d.retrieve("ajax_url"))
            return;
        d.addEvent("click", function(){
            if(!confirm(__('POST_DELETE_CONFIRM')))
                return;
            new Request.JSON({
                url: this.retrieve("ajax_url"),
                onSuccess: function(data){
                    if(data.success && el){
                        el.set('tween', {
                            property : "opacity",
                            onComplete : function(){
                                el.destroy();
                            }
                        })
                        .get('tween')
                        .start(0);
                    }
                }
            }).get();
        })
        .store("ajax_url", d.href)
        .set("href", "javascript:;");
    },
	
	photoDelete :function(){
		jQuery(".photos .photo-delete").each(function(i,elem){
			jQuery(elem).bind('click',function(e){
				domElem=jQuery(this);
				if(confirm(__("ADMIN_CONFIRM_DELETE"))){
					jQuery.ajax({
							  url: domElem.attr('href'),
							  dataType: 'json',			  
							  type: "GET",
							  async:false,
							  success:function(data) {
									if(data.success){
										thumb=domElem.attr('id').replace("link","");
										jQuery("#thumb"+thumb).fadeOut('slow', function() {
											jQuery(this).remove();
										});
									}
							  }
					  }); 
				}
				return false;
			});
		});
	},	
	
    formEnable : true,
	
    initForm : function(){
        var options = $$("#publish-categories, #publish-group, #publish-private").addClass("hidden");
        $("publish-message")
        .resizable()
        .addEvent("focus", function(){
            if(this.hasClass("publish-message-default")){
                this.removeClass("publish-message-default")
                .store("overtext", this.value);
                this.value = "";
                options.removeClass("hidden");
            }
        })
        .addEvent("blur", function(){
            if(this.value.trim() == ""){
                this.value = this.retrieve("overtext");
                this.addClass("publish-message-default")
                .setStyle("height", 0)
                .fireEvent("keyup");
                options.addClass("hidden");
            }
        });
		
        if($("publish-group")){
            var groupOfficial = $("publish-group-official").addClass("hidden");
            $("publish-group-select").addEvent("change", function(){
                if(this.options[this.options.selectedIndex].hasClass("publish-group-admin"))
                    groupOfficial.removeClass("hidden");
                else
                    groupOfficial.addClass("hidden");
            });
        }
    },
	
    attach : function(type){
        var e = $("publish-stock-attachment-"+type)
        .clone()
        .setStyle("opacity", 0)
        .inject("publish-attachments");
        e.set('tween', {
            duration: 300,
            property : "opacity"
        })
        .get('tween')
        .start(1);
        e.getElements(".publish-attachment-delete")[0].addEvent("click", function(){
            e.set('tween', {
                duration: 300,
                property : "opacity",
                onComplete : function(){
                    e.destroy();
                }
            })
            .get('tween')
            .start(0);
        });
        return e;
    },
	
	
    attachEvent : function(){
        if($$("#publish-form input[name=event_title]").length != 0)
            return;
        this.attach("event");
        new Picker.Date($$("#publish-form input[name=event_start], #publish-form input[name=event_end]"), {
            pickerClass: "datepicker_jqui",
            format: __("PUBLISH_EVENT_DATE_FORMAT"),
            timePicker : true,
            draggable : false
        });
    },
	
	
    attachSurvey : function(){
        if($$("#publish-form input[name=survey_question]").length != 0)
            return;
        this.attach("survey");
        new Picker.Date($$("#publish-form input[name=survey_end]"), {
            pickerClass: "datepicker_jqui",
            format: __("PUBLISH_SURVEY_DATE_FORMAT"),
            timePicker : true,
            draggable : false
        });
        $$("#publish-form .publish-survey-mulitple")[0]
        .addEvent("click", function(){
            $$("#publish-form .publish-survey-answers")[0]
            .removeClass(this.checked ? "publish-survey-answers-unique" : "publish-survey-answers-multiple")
            .addClass(this.checked ? "publish-survey-answers-multiple" : "publish-survey-answers-unique");
        });
        Post.surveyAddAnswer();
    },
    surveyAddAnswer : function(){
        var e = $$("#publish-form .publish-survey-answers li");
        if(e.length == 0)
            return;
        if(e.length > 2)
            $$("#publish-form .publish-survey-anwser-delete").removeClass("hidden");
        e = e[0].clone().inject(e[e.length-1], "before");
        e.getElements("input").set("value", "");
    },
    surveyDelAnswer : function(a){
        var n = $$("#publish-form .publish-survey-answers li").length;
        if(n > 3){
            $(a.parentNode).destroy();
            if(n == 4)
                $$("#publish-form .publish-survey-anwser-delete").addClass("hidden");
        }
    },
	
	
    submitForm : function(){
        $("publish-error").set("html", "").addClass("hidden");
        setTimeout(function(){
            Post.disableForm();
        }, 1);
        return true;
    },
	
    disableForm : function(){
        if(!this.formEnable)
            return;
        this.formEnable = false;
        $$("#publish-form input, #publish-form textarea, #publish-form select").set("disabled", true);
        new Element("div", {
            id : "publish-disabled",
            styles : {
                "position" : "absolute",
                "background-color" : "black",
                "opacity" : 0.2
            }
        })
        .setStyles($("publish-form").getCoordinates())
        .inject($("publish-form"), "after");
    },
	
    enableForm : function(){
        if(this.formEnable)
            return;
        this.formEnable = true;
        $$("#publish-form input, #publish-form textarea, #publish-form select").set("disabled", false);
        $("publish-disabled").destroy();
    },
	
    errorForm : function(errMsg){
        $("publish-error").set("html", errMsg).removeClass("hidden");
        this.enableForm();
    },
	initGalleria:function(data){
		Galleria.loadTheme('../static/js/galleria/themes/classic/galleria.classic.js');
		index=0;
		if((photo=location.hash.match(/^#photo-([0-9]+)$/))){
			for(var j=0; j < data.length; j++){
				if(data[j].id==photo[1]){
					index=j;
				}
			}
		}
		Galleria.run('#galleria', {
			dataSource: data,
			imageCrop:false,
			show: index,
		});
		Galleria.ready(function(){
			this.bind("image", function(e) {
				Post.galleriaComLike("photo-"+this.getData(e.index).id);
			});
		});
		jQuery("#adminView").click(function(){
			jQuery("#galleria").toggle();
			if(jQuery(".photos").hasClass('hidden')){
				jQuery(".photos").removeClass('hidden');
			}
			else{
				jQuery(".photos").addClass('hidden');
			}
			jQuery("#addAdmin").toggle();
		});
	},
	galleriaComLike:function(photoHash){
		//gestion des comment et like
            var photos = $$('.photos');
            if(photos.length == 0)
                return;
            var m = photoHash.match(/^photo-([0-9]+)$/);
            if(m){
                photos[0].addClass('hidden');		
                var i = -1;
                for(var j=0; j < Post.photos.length; j++){
                    if(Post.photos[j].id == m[1]){
                        i = j;
                        break;
                    }
                }
                if(i == -1){
                    location.hash = '';
                }else{
                    Post.currentPhoto = i;
                    var photo = Post.photos[i];
                    // Cas de base : On a jamais aimé    
                    $$('.like-link').removeClass('hidden');
                    $$('.unlike-link').addClass('hidden');
                    // Pour chaques Likes :
                    $$(".post-like").each(function(l){
                        // Cas ou quelqu'un a deja aimé && c'est la photo affiché.
                        if(l.hasClass("post-like-attachment-"+photo.id)){
                            // On affiche la "Like Box"
                            l.removeClass("hidden");
                            // Cas ou on a personnellement aimé
                            if($('like-it-'+photo.id) === null){
                                // On affiche "Je n'aime plus !"
                                $$('.like-link').removeClass('hidden');
                                $$('.unlike-link').addClass('hidden');
                            } else {
                                // On affiche "J'aime"
                                $$('.like-link').addClass('hidden');
                                $$('.unlike-link').removeClass('hidden');
                            }
                        } else {
                            // C'est pas la bonne photo, on cache la "Like Box"
                            l.addClass("hidden");
                        }
                    });
                    $$(".post-comment").each(function(e){
                        if(e.hasClass("post-comment-attachment"+photo.id))
                            e.removeClass("hidden");
                        else
                            e.addClass("hidden");
                    });
                    $$(".post-delete").addClass("hidden");
                }
				
            }else if(photos[0].hasClass('hidden')){
                photos[0].removeClass('hidden');
				
                Post.currentPhoto = -1;
				$$(".post-like").each(function(l){
                        if(l.hasClass("post-like-attachment-0"))
                            l.removeClass("hidden");
                        else
                            l.addClass("hidden");
                });		
                $$(".post-comment").each(function(e){
                    if(e.hasClass("post-comment-attachment0"))
                        e.removeClass("hidden");
                    else
                        e.addClass("hidden");
                });
            }
        if(location.hash.indexOf('#') == 0)
            window.fireEvent('hashchange', location.hash.substr(1));
		
        var prev = function(){
            var i = Post.currentPhoto-1;
            if(i < 0)
                i = Post.photos.length-1;
            location.hash = '#photo-'+Post.photos[i].id;
        };
        var next = function(){
            var i = Post.currentPhoto+1;
            if(i >= Post.photos.length)
                i = 0;
            location.hash = '#photo-'+Post.photos[i].id;
        };
	}
};

var Like = {
    initPostLike: function(post_id){
        var URL_ROOT = $('header-title').getProperty('href');
        var obj = {};
        if(Post.currentPhoto != -1)
            obj.attachment = Post.photos[Post.currentPhoto].id;
        else
            obj.attachment = 0;
        new Request({
            url: URL_ROOT+'ajax/like/'+post_id+'/add',
            onSuccess: function(data){
                if(data == 'true'){
                    // On Change de Bouton de Like->Unlike
                    $('post-like-link-'+post_id).toggleClass('hidden');
                    $('post-unlike-link-'+post_id).toggleClass('hidden');
                    // On Affiche le tout
                    if($('post-like-'+post_id+'-'+obj.attachment) != null){
                        $('post-like-'+post_id+'-'+obj.attachment).removeClass('hidden');
                        $('new-like-container-'+post_id+'-'+obj.attachment).removeClass('hidden');
                        $('like-grammar-'+post_id+'-'+obj.attachment).set('text', 'z');
                    } else {
                        $('post-like-'+post_id+'-all').clone()
                                                      .set('id', 'post-like-'+post_id+'-'+obj.attachment)
                                                      .addClass("post-like-attachment-"+obj.attachment)
                                                      .inject('post-like-'+post_id+'-all','after');
                        $('post-like-'+post_id+'-'+obj.attachment).removeClass('hidden');
                        $$('#post-like-'+post_id+'-'+obj.attachment+' .like-last').set('id', 'like-last-'+post_id+'-'+obj.attachment);
                    }
                } else {
                    alert('Erreur, ajout impossible.');
                }
            }
        }).post(obj);
    },
    initPostComLike: function(post_id, comment_id){
        var URL_ROOT = $('header-title').getProperty('href');
        var obj = {
            comment_id : comment_id
        };
        if(Post.currentPhoto != -1)
                obj.attachment = Post.photos[Post.currentPhoto].id;
        new Request({
            url: URL_ROOT+'ajax/likecom/'+post_id+'/add',
            onSuccess: function(data){
                if(data == 'true'){
                    // On Change de Bouton de Like->Unlike
                    $('post-com-like-link-'+comment_id).toggleClass('hidden');
                    $('post-com-unlike-link-'+comment_id).toggleClass('hidden');
                    // On Affiche le tout
                    var value = parseInt($('post-com-like-val-'+comment_id).get('text'));
                    $('post-com-like-val-'+comment_id).set('text', (++value));
                    $('post-com-like-new-'+comment_id).removeClass('hidden');
                    if(value > 1)
                        $('like-com-conj-'+comment_id).removeClass('hidden');
                    else
                        $('like-com-conj-'+comment_id).addClass('hidden');
                } else {
                    alert('Erreur, ajout impossible.');
                }
            }
        }).post(obj);
    },
    initPostUnlike: function(post_id){
        var URL_ROOT = $('header-title').getProperty('href');
        var obj = {};
        if(Post.currentPhoto != -1)
            obj.attachment = Post.photos[Post.currentPhoto].id;
        else
            obj.attachment = 0;
        new Request({
            url: URL_ROOT+'ajax/like/'+post_id+'/delete',
            onSuccess: function(data){
                if(data  == 'true'){
                    $('post-like-link-'+post_id).toggleClass('hidden');
                    $('post-unlike-link-'+post_id).toggleClass('hidden');
                    // Stuff
                    if(parseInt($('like-last-'+post_id+'-'+obj.attachment).get('text')) == 0)
                        $('post-like-'+post_id+'-'+obj.attachment).destroy();
                    else if(parseInt($('like-last-'+post_id+'-'+obj.attachment).get('text')) > 2) {
                        $('like-grammar-'+post_id+'-'+obj.attachment).set('text', 'nt');
                        $('new-like-container-'+post_id+'-'+obj.attachment).addClass('hidden');
                    } else{
                        $('like-grammar-'+post_id+'-'+obj.attachment).set('text', '');
                        $('new-like-container-'+post_id+'-'+obj.attachment).addClass('hidden');
                    }
                }else {
                    alert('Erreur, ajout impossible.');
                }
            } 
        }).post(obj);
    },
    initPostComUnlike: function(post_id, comment_id){
        var URL_ROOT = $('header-title').getProperty('href');
        var obj = {
            comment_id : comment_id
        };
        if(Post.currentPhoto != -1)
                obj.attachment = Post.photos[Post.currentPhoto].id;
        new Request({
            url: URL_ROOT+'ajax/likecom/'+post_id+'/delete',
            onSuccess: function(data){
                if(data  == 'true'){
                    $('post-com-like-link-'+comment_id).toggleClass('hidden');
                    $('post-com-unlike-link-'+comment_id).toggleClass('hidden');
                    var value = parseInt($('post-com-like-val-'+comment_id).get('text'));
                    $('post-com-like-val-'+comment_id).set('text', (--value));
                    if(value < 1)
                        $('post-com-like-new-'+comment_id).addClass('hidden');
                    else if(value == 1)
                        $('like-com-conj-'+comment_id).addClass('hidden');
                    else
                        $('like-com-conj-'+comment_id).removeClass('hidden');
                } else {
                    alert('Erreur, ajout impossible.');
                }
            }
        }).post(obj);
    },
        
    showAll : function(post_id){
        var photo_id;
        if(Post.currentPhoto != -1)
            photo_id = Post.photos[Post.currentPhoto].id;
        else
            photo_id = 0;
        $('like-show-short-'+post_id+'-'+photo_id).destroy();
        $('like-show-all-'+post_id+'-'+photo_id).removeClass("hidden");
    },
    
    showAllCom : function(){
        var customTips = $$('.likeTooltips');
        var toolTips = new Tips(customTips, {
            offsets: {
                'x': 0, //par défaut : 16
                'y': 0 //par défaut : 16
            },
            fixed: true
        });
    }
};
var Dislike={
    initPostDislike:function(a){
        var b=$("header-title").getProperty("href");
        var c={};
        
        if(Post.currentPhoto!=-1){
            c.attachment=Post.photos[Post.currentPhoto].id
            }else{
            c.attachment=0
            }
            new Request({
            url:b+"ajax/dislike/"+a+"/add",
            onSuccess:function(d){
                if(d=="true"){
                    $("post-dislike-link-"+a).toggleClass("hidden");
                    $("post-undislike-link-"+a).toggleClass("hidden");
                    if($("post-dislike-"+a+"-"+c.attachment)!=null){
                        $("post-dislike-"+a+"-"+c.attachment).removeClass("hidden");
                        $("new-dislike-container-"+a+"-"+c.attachment).removeClass("hidden");
                        $("dislike-grammar-"+a+"-"+c.attachment).set("text","z")
                        }else{
                        $("post-dislike-"+a+"-all").clone().set("id","post-dislike-"+a+"-"+c.attachment).addClass("post-dislike-attachment-"+c.attachment).inject("post-dislike-"+a+"-all","after");
                        $("post-dislike-"+a+"-"+c.attachment).removeClass("hidden");
                        $$("#post-dislike-"+a+"-"+c.attachment+" .dislike-last").set("id","dislike-last-"+a+"-"+c.attachment)
                        }
                    }else{
                alert("Erreur, ajout impossible.")
                }
            }
        }).post(c)
},
initPostComDislike:function(a,b){
    var c=$("header-title").getProperty("href");
    var d={
        comment_id:b
    };
    
    if(Post.currentPhoto!=-1){
        d.attachment=Post.photos[Post.currentPhoto].id
        }
        new Request({
        url:c+"ajax/dislikecom/"+a+"/add",
        onSuccess:function(f){
            if(f=="true"){
                $("post-com-dislike-link-"+b).toggleClass("hidden");
                $("post-com-undislike-link-"+b).toggleClass("hidden");
                var e=parseInt($("post-com-dislike-val-"+b).get("text"));
                $("post-com-dislike-val-"+b).set("text",(++e));
                $("post-com-dislike-new-"+b).removeClass("hidden");
                if(e>1){
                    $("dislike-com-conj-"+b).removeClass("hidden")
                    }else{
                    $("dislike-com-conj-"+b).addClass("hidden")
                    }
                }else{
            alert("Erreur, ajout impossible.")
            }
        }
    }).post(d)
},
initPostUndislike:function(a){
    var b=$("header-title").getProperty("href");
    var c={};
    
    if(Post.currentPhoto!=-1){
        c.attachment=Post.photos[Post.currentPhoto].id
        }else{
        c.attachment=0
        }
        new Request({
        url:b+"ajax/dislike/"+a+"/delete",
        onSuccess:function(d){
            if(d=="true"){
                $("post-dislike-link-"+a).toggleClass("hidden");
                $("post-undislike-link-"+a).toggleClass("hidden");
                if(parseInt($("dislike-last-"+a+"-"+c.attachment).get("text"))==0){
                    $("post-dislike-"+a+"-"+c.attachment).destroy()
                    }else{
                    if(parseInt($("dislike-last-"+a+"-"+c.attachment).get("text"))>2){
                        $("dislike-grammar-"+a+"-"+c.attachment).set("text","nt");
                        $("new-dislike-container-"+a+"-"+c.attachment).addClass("hidden")
                        }else{
                        $("dislike-grammar-"+a+"-"+c.attachment).set("text","");
                        $("new-dislike-container-"+a+"-"+c.attachment).addClass("hidden")
                        }
                    }
            }else{
        alert("Erreur, ajout impossible.")
        }
    }
}).post(c)
},
initPostComUndislike:function(a,b){
    var c=$("header-title").getProperty("href");
    var d={
        comment_id:b
    };
    
    if(Post.currentPhoto!=-1){
        d.attachment=Post.photos[Post.currentPhoto].id
        }
        new Request({
        url:c+"ajax/dislikecom/"+a+"/delete",
        onSuccess:function(f){
            if(f=="true"){
                $("post-com-dislike-link-"+b).toggleClass("hidden");
                $("post-com-undislike-link-"+b).toggleClass("hidden");
                var e=parseInt($("post-com-dislike-val-"+b).get("text"));
                $("post-com-dislike-val-"+b).set("text",(--e));
                if(e<1){
                    $("post-com-dislike-new-"+b).addClass("hidden")
                    }else{
                    if(e==1){
                        $("dislike-com-conj-"+b).addClass("hidden")
                        }else{
                        $("dislike-com-conj-"+b).removeClass("hidden")
                        }
                    }
            }else{
        alert("Erreur, ajout impossible.")
        }
    }
}).post(d)
},
showAll:function(a){
    var b;
    if(Post.currentPhoto!=-1){
        b=Post.photos[Post.currentPhoto].id
        }else{
        b=0
        }
        $("dislike-show-short-"+a+"-"+b).destroy();
    $("dislike-show-all-"+a+"-"+b).removeClass("hidden")
    },
showAllCom:function(){
    var b=$$(".dislikeTooltips");
    var a=new Tips(b,{
        offsets:{
            x:0,
            y:0
        },
        fixed:true
    })
    }
};

var Comment = {
    init : function(e){
        if(e == null)
            e = $$(".post-comments");
        this.initDelete(e);
        if(typeOf(e) == "elements"){
            e.each(function(e){
                Comment.init(e);
            });
            return;
        }
		
        if(e.getElements(".post-comment").length==0)
            e.addClass("hidden");
		
        // Submit form
        var f = e.getElements("form");
        if(f.length == 0)
            return;
        f = f[0];
        var t = f.getElements("textarea")[0];
        f.addEvent("submit", function(){
            // Disabling form
            f.getElements("input, textarea").set("disabled", true);
            // Sending form trough AJAX
            var obj = {
                message: t.value
            };
            if(Post.currentPhoto != -1)
                obj.attachment = Post.photos[Post.currentPhoto].id;
            new Request({
                url: f.action,
                onSuccess: function(data){
                    var el = new Element("div", {
                        html: data
                    }).getElements("div")[0];
                    el.inject(f, "before");
                    Comment.initDelete(el);
                    f.getElements("input, textarea").set("disabled", false);
                    t.set("value", "").fireEvent("blur");
                }
            }).post(obj);
            return false;
        });
    },
	
    initDelete : function(el){
        if(el == null)
            el = $$(".post-comment");
        if(typeOf(el) == "elements"){
            el.each(function(e){
                Comment.initDelete(e);
            });
            return;
        }
        if(!el.hasClass("post-comment")){
            Comment.initDelete(el.getElements(".post-comment"));
            return;
        }
		
        el.getElements(".post-comment-delete").each(function(d){
            if(d.retrieve("ajax_url"))
                return;
            d.addEvent("click", function(){
                if(!confirm(__('POST_COMMENT_DELETE_CONFIRM')))
                    return;
                new Request.JSON({
                    url: this.retrieve("ajax_url"),
                    onSuccess: function(data){
                        if(data.success && el){
                            el.set('tween', {
                                property : "opacity",
                                onComplete : function(){
                                    el.destroy();
                                }
                            })
                            .get('tween')
                            .start(0);
                        }
                    }
                }).get();
            })
            .store("ajax_url", d.href)
            .set("href", "javascript:;");
        });
		
    },
	
    write : function(post_id){
        var e = $$("#post-"+post_id+" .post-comments")[0].removeClass("hidden"),
        placeholder = e.getElements(".post-comment-write-placeholder")[0].addClass("hidden"),
        avatar = e.getElements(".post-comment-write .avatar")[0].removeClass("hidden"),
        message = e.getElements(".post-comment-write-message")[0].removeClass("hidden");
        t = message.getElements("textarea")[0]
        .setStyle("height", 20);
        t.focus();
        if(t.retrieve("initiated"))
            return;
        t	.store("initiated", true)
        .resizable()
        .addEvent("blur", function(){
            if(this.value.trim() == ""){
                placeholder.removeClass("hidden");
                avatar.addClass("hidden");
                message.addClass("hidden");
            }
        });
    },
	
    showAll : function(post_id){
        $("post-"+post_id+"-comment-show-all").destroy();
        $$("#post-"+post_id+" .post-comment").removeClass("hidden");
    }
};


var Survey = {
    init : function(e){
        if(e == null)
            e = $$(".survey");
        if(typeOf(e) == "elements"){
            e.each(function(e){
                Survey.init(e);
            });
            return;
        }
		
        var inputs = e.getElements("input[type=checkbox], input[type=radio]"),
        nb_votes = inputs.filter(function(el){
            return !! el.checked;
        }).length;
        Survey.showResults(e, nb_votes > 0 || inputs.length == 0);
        if(inputs.length == 0)
            return;
        e.getElements(".survey-choice-vote a")[0].addEvent("click", function(){
            Survey.showResults(e, true);
        });
        e.getElements(".survey-choice-results a")[0].addEvent("click", function(){
            Survey.showResults(e, false);
        });
		
        // Submit form
        e.addEvent("submit", function(){
            var o = {};
            e.getElements("input[type=checkbox], input[type=radio]").filter(function(el){
                return !! el.checked;
            }).each(function(i){
                o[i.name] = i.value;
            });
            // Disabling form
            e	.addClass("survey-disabled")
            .getElements("input").set("disabled", true);
            // Sending form trough AJAX
            new Request({
                url: e.action,
                onSuccess: function(data){
                    var el = new Element("div", {
                        html: data
                    }).getElements("div")[0],
                    ex = $(el.id);
                    el.inject(ex, "after");
                    ex.destroy();
                    Survey.init(el.getElements(".survey"));
                }
            }).post(o);
            return false;
        });
    },
	
    /**
	 * Change the view of th survey
	 *
	 * @param Element e		Form element of the survey
	 * @param boolean b		If true, results are shown, if false, form is shown
	 */
    showResults : function(e, b){
        e.getElements(b	 ? ".survey-answer-vote, .survey-choice-vote" : ".survey-answer-result, .survey-choice-results").addClass("hidden");
        e.getElements(!b ? ".survey-answer-vote, .survey-choice-vote" : ".survey-answer-result, .survey-choice-results").removeClass("hidden");
    }
};



var Calendar = {
    init : function(){
        var e = $$('#calendar table a');
        e.each(function(el){
            var content = el.get('title').split(' :: ');
            el.store('tip:title', content.splice(0, 1)[0]);
            el.store('tip:text', '<ul><li>'+content.join('</li><li>')+'</li></ul>');
        });
        new Tips(e);
    }
};

var Group = {
    initEdit : function(){
        if(!$("group_edit_name"))
            return;
		
        $("group_edit_description").resizable();
        this.initDeleteMember();
		
        // Creation date
        new Picker.Date($("group_edit_creation_date"), {
            pickerClass: "datepicker_jqui",
            format: __("GROUP_EDIT_FORM_CREATION_DATE_FORMAT_PARSE"),
            draggable : false
        });
		
        // Sortable list of members
        this.sortableMembers = new Sortables('#group-edit-members ul', {
            constrain: true,
            clone: true,
            revert: true,
            handle: '.group-member-handle'
        });
		
        // User name auto-completion
        new Meio.Autocomplete('group_edit_add_member', $('group_edit_add_member_url').value, {
            delay: 200,
            minChars: 1,
            cacheLength: 100,
            maxVisibleItems: 10,
			
            onSelect: function(elements, data){
                var i = $('group_edit_add_member').set('value', '');
                i.blur();
                setTimeout(function(){
                    i.focus();
                }, 0);
				
                var e = new Element('li', {
                    html: $("group-edit-member-stock").innerHTML
                });
                e.getElements('.group-member-name')[0]
                .set('html', data.value.htmlspecialchars())
                .set('href', data.url)
                .removeClass('group-member-name');
                e.getElements('input[name=members_ids[]]')[0]
                .set('value', data.user_id);
                e.getElements('input[name=member_title]')[0]
                .set('name', 'member_title_'+data.user_id);
                e.getElements('input[name=member_admin]')[0]
                .set('name', 'member_admin_'+data.user_id);
                Group.initDeleteMember(e);
                e.inject($$('#group-edit-members ul')[0]);
                Group.sortableMembers.addItems(e);
            },
			
            urlOptions: { 
                queryVarName: 'q',
                max: 10
            },
            filter: {
                filter: function(text, data){
                    return true;
                },
                formatMatch: function(text, data, i){
                    return data.value;
                },
                formatItem: function(text, data){
                    return data.value;
                }
            }
        });

    },
	
    initDeleteMember : function(el){
        if(el == null)
            el = $$("#group-edit-members li");
        if(typeOf(el) == "elements"){
            el.each(function(e){
                Group.initDeleteMember(e);
            });
            return;
        }
        el.getElements(".group-member-delete")[0].addEvent("click", function(){
            Group.sortableMembers.removeItems(el);
            el.destroy();
        });
    }
};




var User = {
    initEdit : function(){
        if(!$("user_edit_mail"))
            return;
		
        // Creation date
        new Picker.Date($("user_edit_birthday"), {
            pickerClass: "datepicker_jqui",
            format: __("USER_EDIT_FORM_BIRTHDAY_FORMAT_PARSE"),
            draggable : false
        });
		
    }
};

var data = [
    {identifier: 1, value: 'some1'},
    {identifier: 2, value: 'some2'},
    {identifier: 3, value: 'some3'}
];
        
var Extra = {
    init : function(){
        var URL_ROOT = $('header-title').getProperty('href');
        $$('#isepor .autocomplete').each(function(el){
            var val = el.getParent().get('itemid');
            var type = $('question-'+val+'-type').get('value');
            var extra = $('question-'+val+'-extra').get('value');
            extra = (extra.length == 0) ? '': extra;
            new Meio.Autocomplete(el, URL_ROOT+'ajax/isepor/autocomplete', {
                delay: 200,
                minChars: 0,
                cacheType: 'own',
                cacheLength: 100,
                maxVisibleItems: 10,
                onNoItemToList: function(elements){
                   // alert('Not Found :'+elements.toSource());
                   $('question-'+val+'-valid').set('value', '');
                   $('question-'+val+'-input').addClass('form-error');
                   $('question-'+val+'-error-com').addClass('hidden');
                   $('question-'+val+'-error-emp').addClass('hidden');
                   $('question-'+val+'-error-nan').removeClass('hidden');
                }, // this event is fired when theres no option to list
                onSelect: function(elements, value){
                    //alert('Selected ! Val :'+value.toSource());
                    $('question-'+val+'-input').set('class', '');
                    $('question-'+val+'-error-com').addClass('hidden');
                    $('question-'+val+'-error-emp').addClass('hidden');
                    $('question-'+val+'-error-nan').addClass('hidden');
                    $('question-'+val+'-valid').set('value', value.valid);
                    if($('question-'+val+'-extra'))
                        var has_extra = '-extra';
                    else 
                        var has_extra = '';
                    $('question-'+val+'-valid').set('name', 'valid-'+value.tableName+'-'+val+has_extra);
                }, // this event is fired when you select an option
                onDeselect: function(elements){
                    //alert('Deselected : '+elements);
                    $('question-'+val+'-valid').set('value', '');
                    $('question-'+val+'-input').addClass('form-error');
                    $('question-'+val+'-error-com').removeClass('hidden');
                    $('question-'+val+'-error-emp').addClass('hidden');
                    $('question-'+val+'-error-nan').addClass('hidden');
                }, // this event is fired when you deselect an option 	
                urlOptions: { 
                    queryVarName: 'q',
                    extraParams: [{
                            name: 'type',
                            value: type
                        }, {
                            name: 'extra',
                            value: extra
                    }],
                    max: 10
                },
                filter: {
                    filter: function(text, data){
                        return true;
                    },// filters the data array
                    formatMatch: function(text, data, i){
                        return data.shows;
                    },// this function should return the text value of the data element
                    formatItem: function(text, data){
                        return data.shows;
                    }// the return of this function will be applied to the 'html' of the li's
                },
                fieldOptions: {
                    classes: {
                        loading: 'form-loading', // applied to the field when theres an ajax call being made
                        selected: 'form-ok' // applied to the field when theres a selected value
                    }
                }, 
                listOptions: {
                    width: 'field', // you can pass any other value settable by set('width') to the list container

                    classes: {
                        container: 'ma-container',
                        hover: 'ma-hover', // applied to the focused options
                        odd: 'ma-odd', // applied to the odd li's
                        even: 'ma-even' // applied to the even li's
                    }
                },
                requestOptions: {
                    formatResponse: function(jsonResponse){ // this function should return the array of autocomplete data from your jsonResponse
                        return jsonResponse;
                    }
                }
            });
        });
    }
}

var Search = {
    init : function(){
        // Search field auto-completion
        new Meio.Autocomplete('search', $('search-ajax-url').value, {
            delay: 200,
            minChars: 1,
            cacheLength: 100,
            maxVisibleItems: 20,
			
            onSelect: function(elements, data){
                document.location = data.url
                $('search').set('value', '').blur();
            },
			
            urlOptions: { 
                queryVarName: 'q',
                max: 20
            },
            filter: {
                filter: function(text, data){
                    return true;
                },
                formatMatch: function(text, data, i){
                    return data.value;
                },
                formatItem: function(text, data){
                    return data.value;
                }
            },
            listOptions: { 
                width: 300
            }
        });
		
    }
};

// Set the width of videos in the timelines to 100%
function resizeVideos(){
    $$(".timeline .video").each(function(e){
        e	.setStyle("width", "100%")
        .setStyle("height", e.offsetWidth * 3/4);
    });
}



window.addEvent("domready", function(){

    // Search form
    $("search").addEvent("focus", function(){
        if(this.hasClass("search-default")){
            this.removeClass("search-default")
            .store("overtext", this.value);
            this.value = "";
        }
    })
    .addEvent("blur", function(){
        if(this.value.trim() == ""){
            this.value = this.retrieve("overtext");
            this.addClass("search-default");
        }
    });
	
    // Extra
    
    Extra.init();
    
    // Posts
    Post.init();
	
    // Comments
    Comment.init();
	
    // Surveys
    Survey.init();
	
    // Calendar
    Calendar.init();
	
    // Search
    Search.init();
	
    // Likes 
    Like.showAllCom();	
	
    // Video resizing
    resizeVideos();
	
});

// Video auto-resizing
window.addEvent("resize", resizeVideos);

// Field Verif For Isep Live's poll Isep D'Or

window.addEvent('submit', function(e) {
    if($('form-isepor-first')){
        $$('#isepor .valid').each(function(el){
            // if (typeOf(el.get('value'))=='undefined' || !el.get('value') || el.get('value').trim().length == 0) {
                // var val = el.getParent().get('itemid');
                // $('question-'+val+'-error-com').addClass('hidden');
                // $('question-'+val+'-error-nan').addClass('hidden');
                // $('question-'+val+'-error-emp').removeClass('hidden');
                // new Event(e).stop();
            // }
        });
    } else if($('form-isepor-final')){
//        $$('#isepor .valid').each(function(el){
//            var val = el.get('itemid');
//            var valid = false;
//            $$('#question-'+val+' input[type=radio]').each(function(el){
//                if(el.checked)
//                    valid = true;
//            });
//            if(!valid) {
//                $('question-'+val+'-error-emp').removeClass('hidden');
//                new Event(e).stop();
//            } else {
//                $('question-'+val+'-error-emp').addClass('hidden');
//            }
//        });
    }
});

//Fonctions requises pour la page Administration
var Admin= {
	adminsInit:function(){
		//autocomplétion pour ajout d'admin
		var type=$('type').get('value');
		new Meio.Autocomplete('admin_edit_add_admin', $('admin_edit_add_admin_url').value, {
            delay: 200,
            minChars: 1,
            cacheLength: 100,
            maxVisibleItems: 10,
            onSelect: function(elements, data){
				$('admin_edit_add_admin').addClass('form-ok');
				$('admin_edit_add_admin').removeClass('form-error');
				$('error-com').addClass('hidden');
				$('valid').set('value', data.valid);
            },
			onDeselect: function(elements){
				$('valid').set('value', '');
				$('admin_edit_add_admin').addClass('form-error');
				$('admin_edit_add_admin').addClass('form-ok');
				$('error-nan').addClass('hidden');
			},
			
			onNoItemToList: function(elements){
			   $('valid').set('value', '');
			   $('admin_edit_add_admin').addClass('form-error');
			   $('error-nan').removeClass('hidden');
			}, 
				
            urlOptions: { 
                queryVarName: 'q',
				extraParams: [{
						name: 'type',
						value: type
				}],
                max: 10
            },
            filter: {
                filter: function(text, data){
                    return true;
                },
                formatMatch: function(text, data, i){
                    return data.shows;
                },
                formatItem: function(text, data){
                    return data.shows;
                }
            },
			listOptions: {
				width: 'field', // you can pass any other value settable by set('width') to the list container

				classes: {
					container: 'ma-container',
					hover: 'ma-hover', // applied to the focused options
					odd: 'ma-odd', // applied to the odd li's
					even: 'ma-even' // applied to the even li's
				}
			},
			requestOptions: {
				formatResponse: function(jsonResponse){ // this function should return the array of autocomplete data from your jsonResponse
					return jsonResponse;
				}
			}
        });
		
		//bind de l'envoie des données (suppression et ajout);
		jQuery("#admins img").each(function(i,elem){
			jQuery(elem).bind('click',function(){
				if(confirm(__("ADMIN_DELETE_CONFIRM"))){
					window.location.href =jQuery("#form_admins").attr("action")+"?del="+jQuery(this).attr("id");
				}
			});
		});
		jQuery("#form_admins").submit(function(){
			if(jQuery("#form_admins :input[name='valid-students']").val()=="")
				return false;
		});
	},
	
	loadjscssfile:function(filename, filetype){
		 if (filetype=="js"){ //if filename is a external JavaScript file
			  var fileref=document.createElement('script')
			  fileref.setAttribute("type","text/javascript")
			  fileref.setAttribute("src", filename)
			  
		 }
		 else if (filetype=="css"){ //if filename is an external CSS file
			  var fileref=document.createElement("link")
			  fileref.setAttribute("rel", "stylesheet")
			  fileref.setAttribute("type", "text/css")
			  fileref.setAttribute("href", filename)
		 }
		 if (typeof fileref!="undefined")
			jQuery("#container").after(fileref);
	},
	
	loadCatGrid:function(dataCat){
		date=new Date();
		last_promo = ( date.getFullYear()) + 5;
		if( date.getMonth() < 9){
			last_promo -= 1;
		}
			// prepare the data
			var source =
			{
				localdata: dataCat,
				datatype: "json",
				datafields: [
					{ name: 'extra' },
					{ name: 'position',type:'number' },
					{ name: 'questions' },
					{ name: 'students' },
					{ name: 'events' },
					{ name: 'associations' },
					{ name: 'employees' },
					{ name: 'id' },
				],
			};
			var dataAdapter = new jQuery.jqx.dataAdapter(source);
			// initialize CategorieGrid
			jQuery("#categorieGrid").jqxGrid({
				width: 660,
				source: dataAdapter,
				autoheight:true,
				editable: true,
				editmode: 'dblclick',
				selectionmode: 'singlerow',
				rendered: function(type){
                    // select all grid cells.
                    var gridCells = jQuery('#categorieGrid').find('.jqx-grid-cell').parent();
                    // initialize the jqxDragDrop plug-in. Set its drop target to the second Grid.
                    gridCells.jqxDragDrop({
                        appendTo: '#isepdorcat', 
						dragZIndex: 1000, 
                        dropAction:'default',
						initFeedback: function (feedback) {
                            feedback.height(25);
                        },
                        dropTarget: jQuery('#categorieReorderGrid'), 
						revert: true
                    });
                    gridCells.off('dragEnd');

                    // set the new cell value when the dragged cell is dropped over the second Grid.      
                    gridCells.on('dragEnd', function (event) {
                        var value = jQuery(this).find("div:nth-child(3)").text();
						var id = jQuery(this).find("div:nth-child(2)").text();
                        var cell = jQuery("#categorieReorderGrid").jqxGrid('getcellatposition', event.args.pageX, event.args.pageY);
                        if (cell != null) {
                            jQuery("#categorieReorderGrid").jqxGrid('setcellvalue', cell.row, "question", value);
							jQuery("#categorieReorderGrid").jqxGrid('setcellvalue', cell.row, "ids", id);
                        }
                    });
					
                },
				columns: [
				  { text: __("ADMIN_POSITION"), datafield: 'position', cellsalign: 'center',align:"center",editable: false	,width:60,pinned:true },
				  { text: __("ADMIN_ID"), datafield: 'id', cellsalign: 'center',align:"center",editable: false	,width:30,pinned:true },
				  { text: __("ADMIN_QUESTIONS"), columntype: 'textbox',align:"center", datafield: 'questions' ,width:170,},
				  { text: __("ADMIN_PARAM"), datafield: 'extra',align:"center",cellsalign: 'center',columntype: 'dropdownlist',width:80,
							createeditor: function (row, column, editor) {
								// assign a new data source to the combobox.
								var list = [" ", 'soiree', last_promo,last_promo-1,last_promo-2,last_promo-3,last_promo-4];
								editor.jqxDropDownList({ source: list,});
							},
				  },
				  { text: __("ADMIN_EVENT"), datafield: 'events',align:"center", columntype: 'checkbox',width:80 },
				  { text: __("ADMIN_ASSOC"), datafield: 'associations', align:"center",columntype: 'checkbox',width:80},
				  { text: __("ADMIN_ELEVES"), datafield: 'students', align:"center",columntype: 'checkbox',width:80},
				  { text: __("ADMIN_EMPLOYEES"), datafield: 'employees',align:"center", columntype: 'checkbox', width:80}
			   ]
			});
			var pos=new Array();
			for(i=0;i<dataCat.length;i++){
				pos[i]=new Array();
				pos[i]["pos"]=i+1;
				pos[i]["questions"]="";
				pos[i]["ids"]="";
			}
			// initialize reorder categorie
			jQuery("#categorieReorderGrid").jqxGrid(
			{
				width: 250,
				autoheight:true,
				selectionmode: 'singlerow',
				source: { 
					localdata: pos,
					datatype:"array",
					datafields:[
						{ name: 'pos',type:'number' },
						{ name: 'question'},
						{ name: 'ids'}
					]
                },
				columns: [
				  { text: __("ADMIN_POSITION"), datafield: 'pos', align:"center",cellsalign: 'center'	,width:60 },
				  { text: __("ADMIN_ID"), datafield: 'ids', cellsalign: 'center',width:30,align:"center", },
				  { text: __("ADMIN_QUESTIONS"), columntype: 'textbox',align:"center", datafield: 'question' },
			   ]
			});
			
			jQuery("#addrowCat").bind('click', function () {
				var data=new Array();
				data[0]=new Array();
				data[0]["pos"]=jQuery('#categorieReorderGrid').jqxGrid('getrows').length+1;
				data[0]['id']=" ";
				data[0]['questions']="";
                jQuery("#categorieReorderGrid").jqxGrid('addrow', jQuery('#categorieReorderGrid').jqxGrid('getrows').length, data);
				jQuery("#categorieReorderGrid").jqxGrid('renderer');	
				data[0]["position"]=" ";
				data[0]['id']=" ";
				data[0]['questions']="";
				data[0]['parametres']="NULL";
				data[0]['events']=0;
				data[0]['students']=0;
				data[0]['employees']=0;
				data[0]['associations']=0;
				jQuery("#categorieGrid").jqxGrid('addrow', jQuery('#categorieReorderGrid').jqxGrid('getrows').length, data);
            });
			jQuery("#delrowCat").bind('click', function () {
				var selectedrowindex = jQuery("#categorieGrid").jqxGrid('getselectedrowindex');
                var rowscount = jQuery("#categorieGrid").jqxGrid('getdatainformation').rowscount;
                if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                    var id=jQuery("#categorieGrid").jqxGrid('getrowid', selectedrowindex);
                    jQuery("#categorieGrid").jqxGrid('deleterow', id);
					jQuery("#categorieReorderGrid").jqxGrid('deleterow', jQuery('#categorieReorderGrid').jqxGrid('getrows').length-1);
                }
			});
			
			jQuery("#saveNoOrderCat").bind('click', function () {
				ok=0;
				gridData=jQuery("#categorieGrid").jqxGrid('getrows');
				jQuery("#errorsCat").addClass('hidden');
				jQuery("#errorsCat").html("");
				data=new Array();
				for(i=0;i<gridData.length;i++){
					if(jQuery.trim(gridData[i].questions)!=""){
						data[i]={};
						data[i]['id']=jQuery.trim(gridData[i].id);
						data[i]['position']=i+1;
						data[i]['questions']=gridData[i].questions;
						data[i]['extra']=jQuery.trim(gridData[i].extra);
						data[i]['type']="";
						if(gridData[i].students==1){
							data[i]['type']+="students";
						}
						if(gridData[i].events==1){
							if(data[i]['type'].length>0){
								data[i]['type']+=",";
							}
							data[i]['type']+="events";
						}
						if(gridData[i].employees==1){
							if(data[i]['type'].length>0){
								data[i]['type']+=",";
							}
							data[i]['type']+="employees";
						}
						if(gridData[i].associations==1){
							if(data[i]['type'].length>0){
								data[i]['type']+=",";
							}
							data[i]['type']+="associations";
						}
						
					}
					else{
						ok=1;
						jQuery("#errorsCat").append(__("ADMIN_ISEPDOR_EMPTY_QUESTION")+" <br/>");
						jQuery("#errorsCat").removeClass('hidden');
					}
				}
				if(ok==0){
					jQuery.ajax({
						  url: jQuery("#pageUrl").val(),
						  dataType: 'text',			  
						  type: "POST",
						  data: {categories: JSON.stringify(data) } ,
						  async:false,
						  cache: false,
						  success:function() {
							window.location.reload()
						  }
					});
				}
			});
			
			jQuery("#saveOrderCat").bind('click', function () {
				ok=0;
				gridData=jQuery("#categorieGrid").jqxGrid('getrows');
				jQuery("#errorsCat").addClass('hidden');
				jQuery("#errorsCat").html("");
				data=new Array();
				for(i=0;i<gridData.length;i++){
					var question=jQuery.trim(gridData[i].questions);
					if(question!=""){
						data[i]={};
						data[i]['id']=jQuery.trim(gridData[i].id);
						if((position=Admin.getOrderPosition(gridData[i].id,question))){
							data[i]['position']=position;
						}
						else{
							ok=1;
							jQuery("#errorsCat").append(__("ADMIN_ISEPDOR_EMPTYORDER_QUESTION") +question+"<br/>");
							jQuery("#errorsCat").removeClass('hidden');
						}
						data[i]['questions']=question;
						data[i]['extra']=jQuery.trim(gridData[i].extra);
						data[i]['type']="";
						if(gridData[i].students==1){
							data[i]['type']+="students";
						}
						if(gridData[i].events==1){
							if(data[i]['type'].length>0){
								data[i]['type']+=",";
							}
							data[i]['type']+="events";
						}
						if(gridData[i].employees==1){
							if(data[i]['type'].length>0){
								data[i]['type']+=",";
							}
							data[i]['type']+="employees";
						}
						if(gridData[i].associations==1){
							if(data[i]['type'].length>0){
								data[i]['type']+=",";
							}
							data[i]['type']+="associations";
						}
						
					}
					else{
						ok=1;
						jQuery("#errorsCat").html(__("ADMIN_ISEPDOR_EMPTY_QUESTION")+"<br/>");
						jQuery("#errorsCat").removeClass('hidden');
					
					}
				}
				if(ok==0){
					jQuery.ajax({
						  url: jQuery("#pageUrl").val(),
						  dataType: 'text',			  
						  type: "POST",
						  data: {categories: JSON.stringify(data) } ,
						  async:false,
						  cache: false,
						  success:function() {
							window.location.reload()
						  }
					});
					
				}
			});
	},
	getOrderPosition:function (id,name){
		gridOrderData=jQuery("#categorieReorderGrid").jqxGrid('getrows');
		for(j=0;j<gridOrderData.length;j++){
			position=gridOrderData[j].pos;
			if(jQuery.trim(id)!="" && gridOrderData[j].ids==id && gridOrderData[j].question==name){
				return position;
			}
			else if(jQuery.trim(id)=="" && gridOrderData[j].question==name){
				return position;
			}
		}
		return false;
	},
	loadEventGrid:function(dataEvent){
			// prepare the data
			var source =
			{
				localdata: dataEvent,
				datatype: "json",
				datafields: [
					{ name: 'extra' },
					{ name: 'name' },
					{ name: 'id' },
				],
			};
			var dataAdapter = new jQuery.jqx.dataAdapter(source);
			// initialize eventGrid
			jQuery("#eventGrid").jqxGrid({
				width: 360,
				source: dataAdapter,
				autoheight:true,
				editable: true,
				editmode: 'dblclick',
				selectionmode: 'singlerow',
				columns: [
				  { text: __("ADMIN_ID"), datafield: 'id', cellsalign: 'center',align:"center",editable: false	,width:30,pinned:true },
				  { text: __("ADMIN_EVENT"), columntype: 'textbox',align:"center", datafield: 'name' ,width:250,},
				  { text: __("ADMIN_ISEPDOR_SOIREE"), datafield: 'extra',align:"center", columntype: 'checkbox',width:80 },
			   ]
			});
			jQuery("#addrowEvent").bind('click', function () {
				var data=new Array();
				data[0]=new Array();
				data[0]["extra"]=0;
				data[0]['id']="";
				data[0]['name']="";
                jQuery("#eventGrid").jqxGrid('addrow', jQuery('#eventGrid').jqxGrid('getrows').length, data);
            });
			jQuery("#delrowEvent").bind('click', function () {
				var selectedrowindex = jQuery("#eventGrid").jqxGrid('getselectedrowindex');
                var rowscount = jQuery("#eventGrid").jqxGrid('getdatainformation').rowscount;
                if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                    var id=jQuery("#eventGrid").jqxGrid('getrowid', selectedrowindex);
                    jQuery("#eventGrid").jqxGrid('deleterow', id);
                }
			});
			jQuery("#saveEvent").bind('click', function () {
				ok=0;
				gridData=jQuery("#eventGrid").jqxGrid('getrows');
				jQuery("#errorsEvent").addClass('hidden');
				jQuery("#errorsEvent").html("");
				data=new Array();
				for(i=0;i<gridData.length;i++){
					if(jQuery.trim(gridData[i].name)!=""){
						data[i]={};
						data[i]['id']=jQuery.trim(gridData[i].id);
						data[i]['name']=gridData[i].name;
						data[i]['extra']=gridData[i].extra;						
					}
					else{
						ok=1;
						jQuery("#errorsEvent").html(__("ADMIN_ISEPDOR_EMPTY_QUESTION")+"<br/>");
						jQuery("#errorsEvent").removeClass('hidden');
					}
				}
				if(ok==0){
					jQuery.ajax({
						  url: jQuery("#pageUrl").val(),
						  dataType: 'text',			  
						  type: "POST",
						  data: {events: JSON.stringify(data) } ,
						  async:false,
						  cache: false,
						  success:function() {
							window.location.reload()
						  }
					});
				}
			});

	},
	loadEmployGrid:function(dataEmploy){
			// prepare the data
			var source =
			{
				localdata: dataEmploy,
				datatype: "json",
				datafields: [
					{ name: 'firstname' },
					{ name: 'lastname' },
					{ name: 'username' },
					{ name: 'id' },
				],
			};
			var dataAdapter = new jQuery.jqx.dataAdapter(source);
			// initialize eventGrid
			jQuery("#employGrid").jqxGrid({
				width: 430,
				source: dataAdapter,
				autoheight:true,
				editable: true,
				editmode: 'dblclick',
				selectionmode: 'singlerow',
				columns: [
				  { text: __("ADMIN_ID"), datafield: 'id', cellsalign: 'center',align:"center",editable: false	,width:30,pinned:true },
				  { text: __("ADMIN_ISEPDOR_USERNAME"), datafield: 'username', cellsalign: 'center',align:"center",editable: false	,width:100,pinned:true },
				  { text: __("ADMIN_ISEPDOR_FIRSTNAME"), align:"center", datafield: 'firstname' ,width:150,},
				  { text: __("ADMIN_ISEPDOR_LASTNAME"), datafield: 'lastname',align:"center", width:150 },
			   ]
			});
			jQuery("#addrowEmploy").bind('click', function () {
				var data=new Array();
				data[0]=new Array();
				data[0]["firstname"]="";
				data[0]['lastname']="";
				data[0]['username']="";
				data[0]['id']="";
                jQuery("#employGrid").jqxGrid('addrow', jQuery('#employGrid').jqxGrid('getrows').length, data);
            });
			jQuery("#delrowEmploy").bind('click', function () {
				var selectedrowindex = jQuery("#employGrid").jqxGrid('getselectedrowindex');
                var rowscount = jQuery("#employGrid").jqxGrid('getdatainformation').rowscount;
                if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                    var id=jQuery("#employGrid").jqxGrid('getrowid', selectedrowindex);
                    jQuery("#employGrid").jqxGrid('deleterow', id);
                }
			});
			jQuery("#saveEmploy").bind('click', function () {
				ok=0;
				gridData=jQuery("#employGrid").jqxGrid('getrows');
				jQuery("#errorsEvent").addClass('hidden');
				jQuery("#errorsEvent").html("");
				data=new Array();
				for(i=0;i<gridData.length;i++){
					if(jQuery.trim(gridData[i].firstname)!="" && jQuery.trim(gridData[i].lastname)!=""){
						data[i]={};
						data[i]['id']=jQuery.trim(gridData[i].id);
						data[i]['firstname']=gridData[i].firstname;
						data[i]['lastname']=gridData[i].lastname;						
					}
					else{
						ok=1;
						jQuery("#errorsEvent").html(__("ADMIN_ISEPDOR_EMPTY_QUESTION")+"<br/>");
						jQuery("#errorsEvent").removeClass('hidden');
					}
				}
				if(ok==0){
					jQuery.ajax({
						  url: jQuery("#pageUrl").val(),
						  dataType: 'text',			  
						  type: "POST",
						  data: {employees: JSON.stringify(data) } ,
						  async:false,
						  cache: false,
						  success:function() {
							window.location.reload()
						  }
					});
				}
			});
	},
	loadDate:function(dataDate){
			jQuery("#first1").jqxDateTimeInput({ 
				width: '120px', 
				height: '20px',	
				textAlign: "center",
				culture: 'fr-FR'
			});
			jQuery("#first2").jqxDateTimeInput({ 
				width: '120px', 
				height: '20px',
				textAlign: "center",
				culture: 'fr-FR'
			});
			jQuery("#third1").jqxDateTimeInput({ 
				width: '120px', 
				height: '20px',
				textAlign: "center",
				culture: 'fr-FR'
			});
			jQuery("#third2").jqxDateTimeInput({ 
				width: '120px', 
				height: '20px',
				textAlign: "center",
				culture: 'fr-FR'
			});
			jQuery("#second1").jqxDateTimeInput({ 
				width: '120px', 
				height: '20px',
				textAlign: "center",
				culture: 'fr-FR'
			});
			jQuery("#second2").jqxDateTimeInput({ 
				width: '120px', 
				height: '20px',
				textAlign: "center",
				culture: 'fr-FR'
			});
			jQuery('#first1 ').jqxDateTimeInput('setDate',new Date(dataDate[0].start)); 
			jQuery('#first2 ').jqxDateTimeInput('setDate', new Date(dataDate[0].end));
			jQuery('#second1 ').jqxDateTimeInput('setDate', new Date(dataDate[1].start));
			jQuery('#second2 ').jqxDateTimeInput('setDate', new Date(dataDate[1].end));
			jQuery('#third1 ').jqxDateTimeInput('setDate', new Date(dataDate[2].start));
			jQuery('#third2 ').jqxDateTimeInput('setDate', new Date(dataDate[2].end));
			jQuery("#saveDate").bind('click', function () {
				ok=0;
				first1=jQuery('#first1').jqxDateTimeInput('getDate');
				first2=jQuery('#first2').jqxDateTimeInput('getDate');
				second1=jQuery('#second1').jqxDateTimeInput('getDate');
				second2=jQuery('#second2').jqxDateTimeInput('getDate');
				third1=jQuery('#third1').jqxDateTimeInput('getDate');
				third2=jQuery('#third2').jqxDateTimeInput('getDate');
				if((first1>first2) || (second1>second2) || (third1>third2)){
					ok=1;
					jQuery("#errorsDate").html(__("ADMIN_ISEPDOR_ERRORDATE")+"<br/>");
					jQuery("#errorsDate").removeClass('hidden');
				}
				
				if(ok==0){
					data=new Array();
					data[0]=new Array(first1,first2);
					data[1]=new Array(second1,second2);
					data[2]=new Array(third1,third2);
					jQuery.ajax({
						  url: jQuery("#pageUrl").val(),
						  dataType: 'text',			  
						  type: "POST",
						  data: {dates: JSON.stringify(data) } ,
						  async:false,
						  cache: false,
						  success:function() {
							window.location.reload()
						  }
					});
				}
			});
	},
	loadTab:function(){
		var index = jQuery.jqx.cookie.cookie("jqxTabs_jqxWidget");
        if (undefined == index) index = 3;
		jQuery('#adminIsepdorTab').jqxTabs({ 
			selectedItem: index,
			width: '98%',  
			animationType: 'fade',
			autoHeight: true,
			position: 'top' ,
			keyboardNavigation: false
		});
		jQuery("#adminIsepdorTab").bind('selected', function (event) {
			jQuery.jqx.cookie.cookie("jqxTabs_jqxWidget", event.args.item);
		});
	},
	
	loadCrop:function(){
		jQuery('img#adminCrop').Jcrop({
			onSelect:   showCoords,
			bgColor:     'grey',
            bgOpacity:   .4,
			addClass: 'jcrop-dark'
		});
		
		function showCoords(c){
			coord='<strong>X1:</strong>'+Math.round(c.x/0.7)+'<br/>'+
				'<strong>Y1:</strong>'+Math.round(c.y/0.7)+'<br/>'+
				'<strong>X2:</strong>'+Math.round(c.x2/0.7)+'<br/>'+
				'<strong>Y2:</strong>'+Math.round(c.y2/0.7)+'<br/>'+
				'<strong>Width:</strong>'+Math.round(c.w/0.7)+'<br/>'+
				'<strong>Height:</strong>'+Math.round(c.h/0.7)+'<br/>'+
				'<strong style="color:'+color[jQuery('#diplomeTab').jqxTabs('selectedItem')]+'">Color</strong>';
			jQuery('#diplomeTab').jqxTabs('setContentAt', jQuery('#diplomeTab').jqxTabs('selectedItem'), coord); 
			color=new Array();
			color[0]="blue";
			color[1]="red";
			color[2]="green";
			jQuery(".cropShower"+jQuery('#diplomeTab').jqxTabs('selectedItem')).remove();
			shower='<div class="cropShower'+jQuery('#diplomeTab').jqxTabs('selectedItem')+'" style="position:absolute;top:'+c.y+'px;left:'+c.x+'px;width:'+c.w+'px;height:'+c.h+'px;z-index:1000;border:solid 1px '+color[jQuery('#diplomeTab').jqxTabs('selectedItem')]+';"></div>';
			jQuery(".jcrop-holder").prepend(shower);
			
			diplomeData[jQuery('#diplomeTab').jqxTabs('selectedItem')]={},
			diplomeData[jQuery('#diplomeTab').jqxTabs('selectedItem')]["x1"]=c.x/0.7;
			diplomeData[jQuery('#diplomeTab').jqxTabs('selectedItem')]["y1"]=c.y/0.7;
			diplomeData[jQuery('#diplomeTab').jqxTabs('selectedItem')]["x2"]=c.x2/0.7;
			diplomeData[jQuery('#diplomeTab').jqxTabs('selectedItem')]["y2"]=c.y2/0.7;
			diplomeData[jQuery('#diplomeTab').jqxTabs('selectedItem')]["w"]=c.w/0.7;
			diplomeData[jQuery('#diplomeTab').jqxTabs('selectedItem')]["h"]=c.h/0.7;
			diplomeData[jQuery('#diplomeTab').jqxTabs('selectedItem')]["index"]=jQuery('#diplomeTab').jqxTabs('selectedItem');
		};
	},
	loadDiplome:function(data){
		jQuery('#diplomeTab').jqxTabs({ 
			width: '90%',  
			animationType: 'fade',
			height: 150,
			position: 'top' ,
			keyboardNavigation: false
		});
		div=new Array();	
		div[0]="diplomeCat";
		div[1]="diplomeName";
		div[2]="diplomeBirth";
		color=new Array();
		color[0]="blue";
		color[1]="red";
		color[2]="green";
		jQuery('#diplomeTab').ready(function(){ 
			try {
				json = jQuery.parseJSON(data);
				for(i=0;i<data.length;i++){
					x1=data[i].x1;
					y1=data[i].y1;
					x2=data[i].x2;
					y2=data[i].y2;
					w=data[i].w;
					h=data[i].h;
					jQuery("#"+div[i]).html('<strong>X1:</strong>'+Math.round(x1)+'<br/>'+
											'<strong>Y1:</strong>'+Math.round(y1)+'<br/>'+
											'<strong>X2:</strong>'+Math.round(x2)+'<br/>'+
											'<strong>Y2:</strong>'+Math.round(y2)+'<br/>'+
											'<strong>Width:</strong>'+Math.round(w)+'<br/>'+
											'<strong>Height:</strong>'+Math.round(h)+'<br/>'+
											'<strong style="color:'+color[data[i].index]+'">Color</strong>'
					);
					shower='<div class="cropShower'+data[i].index+'" style="position:absolute;top:'+(y1*0.7)+'px;left:'+(x1*0.7)+'px;width:'+(w*0.7)+'px;height:'+(h*0.7)+'px;z-index:1000;border:solid 1px '+color[data[i].index]+';"></div>';
					jQuery(".jcrop-holder").prepend(shower);
				}
			} catch (e) {}
		});
		jQuery("#saveDiplome").bind('click', function () {
			jQuery.ajax({
				  url: jQuery("#pageUrl").val(),
				  dataType: 'text',			  
				  type: "POST",
				  data: {diplomeData: JSON.stringify(diplomeData) } ,
				  async:false,
				  cache: false,
				  success:function() {
					window.location.reload()
				  }
			});
			
		});
	},
	//export des bases isepdor_round1 et isepdor_round2
	exportDB: function(type){
		var URL_ROOT = $('header-title').getProperty('href');
		new Request({
            url: URL_ROOT+'adminexport/'+type,
			onSuccess: function(data){
					
				}
       }).get();
		
	},	
};

//Média navigation bar
var Media= {
	navMediaChange: function(type){
		if(type==1){
			$('showlistall').addClass('hidden');
			$('showlistvideo').removeClass('hidden');
			$('showlistphotos').addClass('hidden');
			$('showlistjournaux').addClass('hidden');
			$('showlistpodcast').addClass('hidden');
		}
		if(type==2){
			$('showlistall').addClass('hidden');
			$('showlistvideo').addClass('hidden');
			$('showlistphotos').removeClass('hidden');
			$('showlistjournaux').addClass('hidden');
			$('showlistpodcast').addClass('hidden');
		}
		if(type==3){
			$('showlistall').addClass('hidden');
			$('showlistvideo').addClass('hidden');
			$('showlistphotos').addClass('hidden');
			$('showlistjournaux').removeClass('hidden');
			$('showlistpodcast').addClass('hidden');
		}
		if(type==4){
			$('showlistall').addClass('hidden');
			$('showlistvideo').addClass('hidden');
			$('showlistphotos').addClass('hidden');
			$('showlistjournaux').addClass('hidden');
			$('showlistpodcast').removeClass('hidden');
		}
	}
};

var Student = {
	slider:function(){
		//initialisation de la page (premier pannau anterieur et curseur)
		jQuery('#sliderStudent').jqxSlider({ min: 0, max: jQuery("#loadPannels").val()*5+4,ticksFrequency: 1, value: 0, step: 1,mode: 'fixed',tooltip: false, showTicks: false,width: jQuery("#main").width()*0.98}).trigger('resize');	
		Student.loadStudents(1); 
		jQuery('.jqx-slider-track').remove();
		jQuery("#sliderContainer").css('visibility','visible');
		jQuery('#sliderStudent').css('visibility','visible');
		
		// fonction qui bind les évènement sur le slideur
		jQuery('.jqx-slider-right').bind('click', function () {
			Student.affectSlide();
		});
		jQuery('.jqx-slider-left').bind('click', function () {
			Student.affectSlide();
		});

	},
	loadStudents:function(index){
		ok=0;
		jQuery.ajax({
			  url: jQuery("#url").val()+'/'+index,
			  dataType: 'html',			  
			  type: "GET",
			  async:false,
			  success:function(data) {
				if(data!=""){
					jQuery("#sliderContainer").append(data);
					jQuery("#sliderContainer").children().css('width',jQuery("#main").width()*0.2).trigger('resize');
					ok=1;
				}
			  }
	  }); 
	  if(ok==1){
		return true;
	  }
	  return false;
	},
	showThumb:function(object,avatar,number,promo){
		name=object.innerHTML;
		topPos=jQuery(object).offset().top-80;
		leftPos=jQuery(object).offset().left+30;
		jQuery("#thumbNailer").css('top',topPos);
		jQuery("#thumbNailer").css('left',leftPos);
		jQuery("#thumbNailer span:nth-child(1)").html('<img src='+avatar+' />');
		jQuery("#thumbNailer span:nth-child(2)").html(name+'<br/><br/>'+__('PROFILE_PROMO')+"&nbsp;"+ promo+'<br/>'+__("PROFILE_STUDENT_NUMBER") +"&nbsp;"+ number);
		jQuery("#thumbNailer").removeClass("hidden");
	},
	
	hiddeThumb:function(){
		jQuery("#thumbNailer").addClass("hidden");	
	},
	
	affectSlide:function(){
			nbPanels=jQuery("#loadPannels").val();
			prev=jQuery("#prev").val();
			cur=jQuery('#sliderStudent').jqxSlider('value');
			pannel=Math.ceil(cur/5);
			maxSlider=pannel*5+4;
			maxPanels=Math.ceil((new Date().getFullYear() -2010)/5)+1;
			//charge dynamiquement les années anterieurs
			if(pannel>nbPanels){
				if(pannel<maxPanels && Student.loadStudents(pannel)){
					jQuery('#sliderStudent').jqxSlider({max:maxSlider});
					jQuery("#loadPannels").val(pannel);
				} 
				else{
					jQuery('#sliderStudent').jqxSlider({value:jQuery('#sliderStudent').jqxSlider('value')-1});
					return
				}
			}			
			// déplace les blocs de chaque promo
			if(prev<cur){
				for(i=jQuery("#sliderContainer").children().length;i>=1;i--){
					if(i!=1){
						newLeft=jQuery("#sliderContainer div:nth-child("+(i-1)+")").offset().left;
					}
					else{
						newLeft=jQuery("#sliderContainer div:nth-child("+i+")").offset().left-jQuery("#sliderContainer div:nth-child("+i+")").width();
					}
					jQuery("#sliderContainer div:nth-child("+i+")").offset({left:newLeft});
					jQuery("#prev").val(cur);
				}				
			}
			if(prev>cur){
				for(i=1;i<=jQuery("#sliderContainer").children().length;i++){
					if(i!=jQuery("#sliderContainer").children().length){
						newLeft=jQuery("#sliderContainer div:nth-child("+(i+1)+")").offset().left;
					}
					else{
						newLeft=jQuery("#sliderContainer div:nth-child("+i+")").offset().left+jQuery("#sliderContainer div:nth-child("+i+")").width();
					}
					jQuery("#sliderContainer div:nth-child("+i+")").offset({left:newLeft});
					jQuery("#prev").val(cur);
				}
			}
	},
};

var Layout={
	init:function(){
		jQuery("#adminNav").bind('mouseover',function(){
				Layout.showMenu();
		});
		jQuery("#adminNav").bind('mouseout',function(){
			if(!jQuery("#adminMenu").is(":hover"))
				Layout.hiddeMenu();
		});
		jQuery("#adminMenu").bind('mouseover',function(){
				Layout.showMenu();
		});
		jQuery("#adminMenu").bind('mouseout',function(){
				Layout.hiddeMenu();
		});


	},
	showMenu:function(){
		width=jQuery("#adminNav").width();
		width += parseInt(jQuery("#adminNav").css("padding-left"), 10) + parseInt(jQuery("#adminNav").css("padding-right"), 10); //Total Padding Width
		width += parseInt(jQuery("#adminNav").css("margin-left"), 10) + parseInt(jQuery("#adminNav").css("margin-right"), 10); //Total Margin Width
		width += parseInt(jQuery("#adminNav").css("borderLeftWidth"), 10) + parseInt(jQuery("#adminNav").css("borderRightWidth"), 10); //Total Border Width
		top=jQuery("#adminNav").offset().top;
		left=jQuery("#adminNav").offset().left;
		jQuery("#adminMenu").width(width);
		jQuery("#adminMenu").css('top',top);
		jQuery("#adminMenu").css('left',left);
		jQuery("#adminMenu").removeClass('hidden'); 
		jQuery("#adminNav").addClass("hovered");
	},
	hiddeMenu:function(){
		jQuery("#adminMenu").addClass('hidden');
		jQuery("#adminNav").removeClass("hovered");
	}
};
	
