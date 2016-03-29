jQuery(document).ready(function() {
	//SLIDESHOW AND TIMER EVENT
	var slideshow = $('.slideshow-container');
	if (slideshow.size() != 0){
		//SLIDESHOW
		slideshow.backstretch([
            //"images/slideshow/covilha_3d.jpg",
		 	"images/slideshow/old_map.jpg",
		 	"images/slideshow/cascais_pi.jpg"
		],{
			duration: 3000, fade: 1000
		});
		
		//TIMER EVENT
		var current = new Date();
		$('.time-to-event').countdown({
			until: new Date(2016, 5, 17, 9, 30, 0),
			layout:'<div class="days"><span class="timer-text">{dn}</span> <br>Dias</div> <div class="hours"><span class="timer-text">{hn}</span> <br>Horas</div> <div class="minutes"><span class="timer-text">{mn}</span> <br>Minutos</div> <div class="seconds"><span class="timer-text">{sn}</span> <br>Segundos</div>'
		});
	}
	
	//EVENT MAP
	if ($('#map').size() != 0){
		var map = L.map('map', {scrollWheelZoom: false}).setView([40.27834, -7.511414], 13);

		var mapquestUrl = 'http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png';
		var subDomains = ['otile1','otile2','otile3','otile4'];
		var mapquestAttrib = 'Data, imagery and map information provided by <a href="http://open.mapquest.co.uk" target="_blank">MapQuest</a>, <a href="http://www.openstreetmap.org/" target="_blank">OpenStreetMap</a> and contributors.';

		var mapquest = new L.TileLayer(mapquestUrl, {maxZoom: 18, attribution: mapquestAttrib, subdomains: subDomains});

		mapquest.addTo(map);

		var marker = L.marker([40.27834, -7.511414]).addTo(map);
		marker.bindPopup("<b>Morada</b><br>Calçada Fonte do Lameiro<br>6201-001 Covilhã<br><br><b>Coordenadas WGS84</b><br>GD: -7.511414; 40.27834<br>GMS: 7º 30' 41.09'' W; 40º 16' 42.02'' N").openPopup();
	}
    
	//MAKE SURE FOOTER IS AT THE BOTTOM OF THE PAGE ON LOAD AND RESIZE
	var footer = $('footer');
	function setFooterPosition() {
		var innerHeight = $(window).height();
		if (footer.position().top < innerHeight - 106){
			footer.css('position', 'fixed');
			footer.css('width', '100%');
		}
	}
	
	$(window).on('load', function(){
		setFooterPosition();
	});
	
	$(window).on('resize', function(){
		footer.css('position', 'inherit');
		setFooterPosition();
	});
	
	
	//VALIDATE AND SUBMIT FORM
	var form = $('#form-inscricao');
	if (form.size() != 0){
		//OVERRIDE VALIDATOR FUNCTION TO SUPPORT FIXED MENU MARGIN
		$.prototype.bootstrapValidator.Constructor.prototype._submit = function() {
            if (!this.isValid()) {
                if ('submitted' == this.options.live) {
                    this.options.live = 'enabled';
                    this._setLiveValidating();
                }

                // Focus to the first invalid field
                if (this.invalidField) {
                	var fieldOffset = $('[name="' + this.invalidField + '"]').offset().top;
                	$(window).scrollTop(fieldOffset - $('.navbar').height() - 40);
                    this.getFieldElements(this.invalidField).focus();
                }
                return;
            }

            this._disableSubmitButtons(true);

            // Call the custom submission if enabled
            if (this.options.submitHandler && 'function' == typeof this.options.submitHandler) {
                // Turn off the submit handler, so user can call form.submit() inside their submitHandler method
                this.$form.off('submit.bootstrapValidator');
                this.options.submitHandler.call(this, this, this.$form, this.$submitButton);
            } else {
                // Submit form
                this.$form.off('submit.bootstrapValidator').submit();
            }
        }
		
		//VALIDATOR
		form.bootstrapValidator({
			live: 'disabled',
			message: 'Este valor não é válido',
			feedbackIcons: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
				nome: {
					validators: {
						notEmpty:{
							message: 'Este campo é obrigatório'
						}
					}
				},
				entidade: {
					validators: {
						notEmpty:{
							message: 'Este campo é obrigatório'
						}
					}
				},
				email: {
					validators: {
						notEmpty:{
							message: 'Este campo é obrigatório'
						},
						emailAddress: {
							message: 'Este endereço de email não é válido'
						}
					}
				},
                funcao: {
                    validators: {
                        notEmpty:{
                            message: 'Este campo é obrigatório'
                        }
                    }
                },
				softsig: {
					validators: {
						notEmpty:{
							message: 'Este campo é obrigatório'
						},
                        stringLength: {
                            message: 'Este campo só aceita no máximo 50 caracteres',
                            min: 1,
                            max: 50
                        }
					}
				},
				ws1: {
					validators: {
						notEmpty:{
							message: 'Este campo é obrigatório'
						}
					}
				},
				so: {
					validators: {
						notEmpty:{
							message: 'Este campo é obrigatório'
						}
					}
				},
				knowhowqgis: {
					validators: {
						notEmpty:{
							message: 'Este campo é obrigatório'
						}
					}
				},
                almoco: {
                    validators: {
                        notEmpty:{
                            message: 'Este campo é obrigatório'
                        }
                    }
                },
			}
		});
		
		//PREVENT PAGE RELOAD
		form.on('submit', function(e){
			e.preventDefault();
		});
		
		$('#submit-form').click(
			function() {
				form.bootstrapValidator('validate');
				if (form.data('bootstrapValidator').isValid() == true){
					$.ajax({
						type: 'POST',
						url: 'services/event-register.php',
						data: form.serialize()+"&interesses="+encodeURI($("#interesses").val()),
						dataType: 'json',
						success: function(response){
							var formAlert = $('#form-alert'); 
							formAlert.css('display', 'block');
							if (response.success == false){
								formAlert.addClass('alert-danger');
								$('#form-alert-icon').addClass('glyphicon-warning-sign');
							} else {
								var formIcon = $('#form-alert-icon'); 
								if (formAlert.hasClass('alert-danger') == true){
									formAlert.removeClass('alert-danger')
									formIcon.removeClass('glyphicon-warning-sign');
								}
								formAlert.addClass('alert-success');
								formIcon.addClass('glyphicon-ok');
								form.slideUp('fast');
							}
							$('#form-alert-text').html(response.message);
						}
					});
				}
			}
		);
		
		$('#reset-form').click(
			function() {
				form.data('bootstrapValidator').resetForm(true);
				$('textarea').val('');
			}
		);
	}
	
	//UPDATE PROGRESS BARS TRANSLATION
	var docsProgress = $('#docs-progress');
	if (docsProgress.size() != 0){
		$.getJSON('services/stats.json', function(data){
			for (var i = 0; i < data.length; i++){
				updateProgress(data[i]);
				updateStatsText(data[i]);
			}
		});
	}
	
	function updateProgress(data) {
		var bar = $('#' + data.project + '-progress');
		if (parseInt(data.stats.completed) < 50){
			bar.addClass('progress-bar-danger');
        } else if (parseInt(data.stats.completed) >= 50 && parseInt(data.stats.completed) < 100){
            bar.addClass('progress-bar-warning');
		} else {
			bar.addClass('progress-bar-success');
		}
		bar.attr('aria-valuenow', data.stats.completed);
		bar.attr('style', 'width:' + data.stats.completed + '%');
		bar.html(Math.round(data.stats.completed*10)/10 + '% Completa');
	}
	
	function updateStatsText(data) {
		var date = $('#' + data.project + '-date');
		var untrans = $('#' + data.project + '-untrans');
		var trans = $('#' + data.project + '-trans');
		
		date.html(data.updated_on);
		untrans.html(data.stats.untrans_entities + ' items por traduzir (' + data.stats.untrans_words + ' palavras)');
		trans.html(data.stats.trans_entities + ' items traduzidos (' + data.stats.trans_words + ' palavras)');
	}
    
    //prettyPhoto
    var imgBox = $("a[rel^='prettyPhoto']");
    if (imgBox.size() > 0){
        imgBox.prettyPhoto({
	        social_tools: false
	    });
    }
});

