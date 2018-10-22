/* 	Autor: Gastón Orellana C.
 *	Fecha: 22-11-2011
 *	Declaración de componentes / llamados de JQuery para el manejo de
 *	login en futbolitis. Se gatilla luego de la carga del DOM de la página */

    $(document).ready (function() {
		
		/* *****
         * LOGIN
         * *****
		 *	url (action) del form
		 *	{"key1" : "val1", "key2" : "val2"}
		 *	función que se invoca cuando es "success"
		 * 	tipo de información (json, xml, etc.)*/
        $("#formulario_login").submit(function() {
            // Quitar el texto "Usuario" por defecto del campo
            if($("#nom_usuario_txt").val() == "Usuario") $("#nom_usuario_txt").val("");
            $.post(
                $(this).attr("action"),
                {	"nom_usuario_txt": $("#nom_usuario_txt").val(),
					"contrasena_txt": $("#contrasena_txt").val() },
					function(data) {
						// Tiempo que muestra el mensaje (delay)
						var segundos = 1;

						if (data.exito == "no") {
							$("#login_error_div").html(data.mensaje).fadeIn("fast").delay(segundos*1000).fadeOut("fast");
						} else {
							window.location.href = 'core/entrarJuego';
						}
					},
					"json"
                );
            if($("#nom_usuario_txt").val() == "") $("#nom_usuario_txt").val("Usuario");

            // Previene el submit standard del navegador y la navegación de páginas
            return false;
        });


        /* *********************
         * REGISTRO DE USUARIOS
         * ******************** */
        /*$('#registro_form').ajaxError(function(e, xhr, opts, error) {
            alert('La petición a la página ' + opts.url + ' ha devuelto el siguiente error: ' + xhr.status + ' - ' + error);
        }); */
        $("#registro_form").submit(function() {
            $.post(
                $(this).attr("action"),
                {
                    "reg_nombre_usuario_txt": $("#reg_nombre_usuario_txt").val(),
                    "reg_contrasena_txt": $("#reg_contrasena_txt").val(),
                    "reg_re_contrasena_txt": $("#reg_re_contrasena_txt").val(),
                    "reg_email_txt": $("#reg_email_txt").val(),
                    "reg_re_email_txt": $("#reg_re_email_txt").val(),
                    "reg_nombre_txt": $("#reg_nombre_txt").val(),
                    "reg_apellido_pat_txt": $("#reg_apellido_pat_txt").val(),
					"reg_apellido_mat_txt": $("#reg_apellido_mat_txt").val(),
					"reg_direccion_txt": $("#reg_direccion_txt").val(),
					"reg_tel_particular_txt": $("#reg_tel_particular_txt").val(),
					"reg_tel_movil_txt": $("#reg_tel_movil_txt").val()
                },
                  function(data) {
					$("#reg_error_div").html(data.mensaje);
				  },
                  "json"
                )
                .error(function() {
                  alert("Ha sucedido un error en el envío de los datos");
                });

            // Previene el submit standard del navegador y la navegación de páginas
            return false;
        });



        /* Genera efecto del campo contraseña
         * Al hacer click sobre el campo, desaparece el campo máscara
         * y muestra el real, que corresponde al campo password */
        $("#contrasena_mask_txt").show();
        $("#contrasena_txt").hide();

        $("#contrasena_mask_txt").focus(function() {
            $("#contrasena_mask_txt").hide();
            $("#contrasena_txt").show();
            $("#contrasena_txt").focus();
        });

        $("#contrasena_txt").blur(function() {
            if($("#contrasena_txt").val() == "") {
                $("#contrasena_mask_txt").show();
                $("#contrasena_txt").hide();
            }
        });

        /* Efecto para cualquier campo que utilice el class "input_form_login"
         * Si se selecciona el campo, lo deja vacío para escribir; si nada
         * se escribe y pierde el foco (blur), vuelve a dejar el campo con el
         * valor por defecto */
        $(".input_form_login").each(function() {
            var default_value = this.value;
            $(this).focus(function() {
                if(this.value == default_value) {
                    this.value = "";
                }
            });
            $(this).blur(function() {
                if(this.value == "") {
                    this.value = default_value;
                }
            });
        });


		
		/* *********************
		 * FORMULARIO DE APUESTA
		 * ********************* */
		$("#form_apostar").submit(function() {
			if(confirm("¿Estás seguro de realizar estas apuestas?")) {
				// Obtiene todos los input del tipo text
				var inputs = $("#form_apostar :input[type=text]");
				
				// Verifica si vale la pena comenzar proceso de apuesta,
				// porque para que se cumpla aquello, debe al menos existir
				// una opción completa, o sea, tanto local como visita
				/*inputs.each(function() {
					alert($.trim($(this).val()));
					alert(this.name);
				});*/
				$.post(
					$(this).attr("action"),
					{
						"numero_partido": $("#numero_partido").val(),
						"inputs": $ .param(inputs)
					},
					  function(data) {
						if(parseInt(data.diferencia) != 0) {
							$("#total_creditos_id").html(data.diferencia);
						}
						var msg = $('<div title="Futbolitis">'+data.mensaje+'</div>');
						msg.dialog({
							modal: true,
							buttons: [
								{
									text: "Ok",
									click: function() { $(this).dialog('close'); }
								}
							]
						});
					  },
					  "json"
					)
					.error(function(x,e) {
						alert("Error al intentar apostar sobre el partido. Si este persiste, por favor contacta al administrador." + x.responseText);
					});
			}
					
            return false;
        });
		 
		// Solo se pueden ingresar números en los campos 
		$("#form_apostar").each(function() {
			$(this).keypress(function(evt) {
				var keynum;
				
				if(window.event) keynum = evt.keyCode; //IE
				else keynum = evt.which;
				
				// previene escribir si no es un número
				if(keynum<47 || keynum>58) evt.preventDefault();
			})
		});
		// Permite utilizar el ENTER para mandar el comentario escrito
		$("#caja_mensaje").attr('maxlength', maxChars);
		$("#caja_mensaje").keyup(function(event) {
			// Cuenta caracteres y no permite más de lo configurado
			var largoActual = $("#caja_mensaje").val().length;
			var diferencia = maxChars-largoActual;
			var keynum;
				
			if(window.event) keynum = event.keyCode; //IE
			else keynum = event.which;
	
			if(keynum == 13) escribirMensaje();

			$("#chars_allowed").html(diferencia + " caracteres disponibles");
		});
		// Evita que se pueda pegar contenido en la caja de comentarios
		$('#caja_mensaje').bind('paste', function(e) {
			e.preventDefault();
		});
		

		/* Aplicar compartamiento para el MENÚ */
        /*$("li").mouseover(function() {
            $(this).stop().animate({height:'150px'},{queue:false, duration:300, easing: 'linear'})
        });
        $("li").mouseout(function(){
            $(this).stop().animate({height:'50px'},{queue:false, duration:300, easing: 'linear'})
        });*/
	
	}); // fin del DOM
	
	
	/* ***********************************************************************************************
	 * ***********************************************************************************************
	 * *********************************************************************************************** */
	 
	// Destaca la fila entera al pasar sobre ella
	function destacarPartido(elemento, sobre) {
		if (sobre) $(elemento).attr("style", "background-color: #FF0000; cursor: pointer");
		else $(elemento).attr("style", "background-color: none");
	}
	
	/* ------------------------------------------------------------------------
	 * Controla la carga del partido seleccionado, dejándolo como el central y
	 * pasando el vigente a la lista de partidos disponibles
	 * ------------------------------------------------------------------------ */
	function cargarPartido3(idPartido) {
		// Parte llendo a buscar si hay apuestas sobre el partido seleccionado
		// Además traerá la hora del servidor para calcular el tiempo restante
		if(!bloqueo) {
			// Bloquea todas las filas mientras se está procesando la actual
			bloqueo = true;
			
			$.post(sitio+"/inicio/cargarPartido/"+idPartido, function(data) {
				var json = jQuery.parseJSON(data);	// parsea la cadena y la deja como objeto JSON válido
				if(json) {
					var arrApuestas = json.apuestas;
					var horaServidor = json.horaServidor;
					var obj = null;			// objeto partido actual
					var objCentral = null;	// objeto partido que aparece en la zona central
					
					// Esconde botón de apuesta
					$("#apostar_btn").hide();
					
					// Configura los objetos
					for(var i=0; i<arrObjPartido.length; i++) {
						var objPartido = arrObjPartido[i];
						if(objPartido.id_partido == idPartido) obj = objPartido;	// Busca el objeto que corresponde al id_partido recibido
						if(objPartido.vigente == 1) objCentral = objPartido;		// Objeto en la pantalla central
					}
					
					// Cronómetro
					if(timer) clearInterval(timer);
					cronometro2(horaServidor, obj.fecha_programada, obj.hora_programada, 'estado_partido', true);
					
					// Elimina fila
					$('#partido_'+obj.id_partido).remove();
						
					// Modifica los valores con respecto al partido seleccionado
					// Muy importante, controla el id_partido que estará disponible para apostar
					$("#numero_partido").val(obj.id_partido);	
					$("#nombre_partido_local").html(obj.nom_equipo_local);
					$("#partido_lugar").html(obj.lugar);
					$("#partido_fecha_programada").html(obj.fecha_programada);
					$("#partido_hora_programada").html(obj.hora_programada);
					$("#nombre_partido_visita").html(obj.nom_equipo_visita);
						
					// Modifica el título si corresponde
					if(obj.destacado == 1) $("#es_destacado").html("Partido DESTACADO de la semana");
					else $("#es_destacado").html("Partido de la semana");
						
					// Agrega la nueva fila
					var tr =	'<tr id="partido_'+objCentral.id_partido+'" onMouseOver=\'destacarPartido(this, true)\' onMouseOut=\'destacarPartido(this, false)\' onClick=\'cargarPartido3('+objCentral.id_partido+')\''+
									'height="25" bgColor="#FFFFFF">'+
									'<td>'+objCentral.nom_equipo_local+'</td>'+
									'<td>v/s</td>'+
									'<td>'+objCentral.nom_equipo_visita+'</td>'+
									'<td>'+
										'<div id="partido_en_lista_'+objCentral.id_partido+'">'+
											'<script>'+
												'cronometro2(\''+horaServidor+'\', \''+objCentral.fecha_programada+'\', \''+objCentral.hora_programada+'\', \'partido_en_lista_'+objCentral.id_partido+'\', false);'+
											'</script>'+
										'</div>'+
									'</td>'+
								'</tr>';
					$("#listado_partidos_disponibles").append(tr);
					$("#partido_"+objCentral.id_partido).fadeIn('slow');
					
					// Deja al objeto como el central y viceversa
					obj.vigente = 1;
					objCentral.vigente = 0;

					// Limpia todos los inputs
					var inputs = $("#form_apostar :input[type=text]");
					inputs.each(function() { $(this).val(""); });
					
					// Si encuentra apuestas, las despliega en los input correspondientes
					for(var i=0; i<arrApuestas.length; i++) {
						var ap = arrApuestas[i];
						$("#opcion-A-"+ap.id_opcion).val(ap.resultado_local); 
						$("#opcion-B-"+ap.id_opcion).val(ap.resultado_visita);
					}
					
					// Vuelve a cargar los comentarios y resetea variables de control
					recargarComentarios(obj.id_partido);
					
					// Libera el bloqueo
					bloqueo = false;
				} else {
					alert("No se ha podido cargar el partido seleccionado. Si el error persiste, por favor contacta al administrador.");
					bloqueo = false;
				}
			});
		}
		
	}
	
	function cronometro2(fechaServidor, fecha, hora, nombre_div, btn_apostar) {
		// Va a buscar la hora al servidor
		var arrHoraServidor = fechaServidor.split(" ");
		var fechaX = arrHoraServidor[0].split("-");
		var horaX = arrHoraServidor[1].split(":");
		
		var elemento = "#"+nombre_div;
		var arrFecha = fecha.split("-");
		var arrHora = hora.split(":");
		
		// año, mes, dia, hora, minuto, segundos, milisegundos
		var tiempoPartido = new Date(arrFecha[2], arrFecha[1]-1, arrFecha[0], arrHora[0], arrHora[1], arrHora[2], 0);
		var tiempoActual = new Date(fechaX[0], fechaX[1]-1, fechaX[2], horaX[0], horaX[1], horaX[2], 0);
		
		// Resta y obtiene el resultado en milisegundos, luego lo redondea
		var diferencia = tiempoPartido.getTime() - tiempoActual.getTime();
		diferencia = Math.floor(diferencia/1000)*1000;
		
		if(diferencia > 0) {
			// Muestra un mensaje mientras aparece el cronómetro
			$(elemento).html("Cargando...").fadeIn('slow');
			if(btn_apostar) {
				// Timer GENERAL
				timer = setInterval(function() {
					//var fechaCronometro = new Date(0,0,0,0,0,0,diferencia);  // milisegundos
					
					if(diferencia <= 0) {
						clearInterval(timer); //timer.stop();
						$(elemento).html("Cerrado");
						$("#apostar_btn").hide();
					} else {
						$(elemento).html(interpretaFecha(tiempoPartido, false));
					}
					
					diferencia -= 1000;	// le resta 1 segundo
				}, 1000);
				
				if(btn_apostar) $("#apostar_btn").show();
			} else {
				// Timers para el listado de partidos
				var timerParticular = setInterval(function() {
					//var fechaCronometro2 = new Date(0,0,0,0,0,0,diferencia);  // milisegundos

					if(diferencia <= 0) {
						clearInterval(timerParticular);
						$(elemento).html("Cerrado");
					} else {					
						$(elemento).html(interpretaFecha(tiempoPartido, true));
					}
					
					diferencia -= 1000;	// le resta 1 segundo
				}, 1000);
			}
		} else {
			// Está cerrado el timer
			$(elemento).html("Cerrado");
			if(btn_apostar) $("#apostar_btn").hide();
		}
    }
	
	function interpretaFecha(fecha, acotado) {
		var salida = "";
		var diaEnMs = 24*60*60*1000;	// 1 día
		
		// Fecha actual
		var fechaActual = new Date();
		
		// Diferencia en milisegundos
		var msFecha = fecha.getTime();
		var msFechaActual = fechaActual.getTime();
		var diferencia = msFecha - msFechaActual;
		
		// Si la diferencia es mayor a 1 día, calcula el número de estos
		if(diferencia >= diaEnMs) {
			// Número de días
			var numDias = Math.floor(diferencia / diaEnMs);
			
			if(acotado) salida = numDias+"d+";
			else salida = "Comienza en más de " + numDias + " días";
			
		} else {
			var objFecha = new Date(0,0,0,0,0,0,diferencia);
			var hora = objFecha.getHours();
			var minuto = objFecha.getMinutes();
			var segundo = objFecha.getSeconds();
			if (hora < 10) { hora = "0" + hora; }
			if (minuto < 10) { minuto = "0" + minuto; }
			if (segundo < 10) { segundo = "0" + segundo; }
			
			if(acotado) salida = hora+"h:"+minuto+"m";
			else salida = "Comienza en " + hora+"h:"+minuto+"m:"+segundo+"s";
		}
		
		return salida;
	}
	
	/* PANEL DE COMENTARIOS PARA UN PARTIDO */
	function cargarComentarios(idPartido) {
		// Limpia la caja de comentarios
		$("#panel_comentarios_inside").html('');
		// Si hay un partido seleccionado comienza el llamado
		if(idPartido != 0) {
			// Llama inmediatamente a la carga de los comentarios
			traeComentarios(idPartido);
			
			// GENERAR SETINTERVAL para ir comprobando por comentarios
			timerComments = setInterval(function() {
				traeComentarios(idPartido);
			}, refresco*1000);
		} else {
			$("#panel_comentarios_inside").html('<div class="no_comentario">Se ha seleccionado ningún partido.</div>');
		}
	}
	// Función que se mantiene constantemente invocando para cargar los comentarios
	function traeComentarios(idPartido) {
		// Hace el llamado por POST y trae los últimos N comentarios del partido
		// Envía el ID del partido por POST para aumentar seguridad
		$.post(
			sitio+'/inicio/cargarComentarios',
			{
				"idPartido": idPartido
			},
			function(data) {
				var total = data.resultado.length;
				// Muestra comentarios
				if(total>0) {					
					var msg = '';
					
					// Crea la cadena de comentarios para visualizar en el panel
					for(var i=0; i<total; i++) {
						var comentario = data.resultado[i];
						msg += '<div id="comentario_fila_'+comentario.id_comentario+'" class="comentario_fila"><span class="com_nombre_usuario">' + comentario.nombre_usuario + '</span>: <span class="com_mensaje">'+comentario.mensaje+'</span><br />'+
								'<span class="com_fecha">'+comentario.fecha+'</span></div>';	
					}
					
					// Si el total de comentarios difiere, es porque hay cambios y se actualiza el panel
					//alert(total+" "+totalCargados);
					if(total != totalCargados) {
						$("#panel_comentarios_inside").html(msg).hide().fadeIn('slow');
						totalCargados = total;
					}
				} else {
					// Basta con HTML porque se estará simplemente reemplazando por el mismo texto
					// y para este caso no habrá más mensajes
					$("#panel_comentarios_inside").html('<div class="no_comentario">Por el momento no hay comentarios para este partido.</div>');
				}
			},
			"json"
		).error(function() {
			// Detiene la actualización de comentarios por error
			//clearInterval(timerComments);
			var m = '<div class="error_comentario">No se pudieron cargar los comentarios. Reintento automático en '+refresco+' segundos aproximadamente</div>';
			$("#panel_comentarios_inside").prepend(m);
		});
	}
	// Almacena el comentario escrito por el jugador
	function escribirMensaje() {
		var mensaje = $.trim($("#caja_mensaje").val());
		var idPartido = $("#numero_partido").val();
		
		// Solo realiza la acción de guardar el mensaje si hay algo escrito
		if(mensaje != "") {
			// Verifica que no esté dentro del intervalo de control de flooding
			
			$.post(
				sitio+"/inicio/escribirComentarios/",
				{
					"caja_mensaje": mensaje,
					"id_partido": idPartido
				},
				function(data) {
					if(data.resultado == "si") {
						// Limpia la caja de mensaje y el mensaje de total de caracteres
						$("#caja_mensaje").val('');
						$("#chars_allowed").html('');
						
						$("#panel_comentarios_inside").prepend('<div class="ok_comentario">'+data.mensaje+'</div>');
					} else {
						$("#panel_comentarios_inside").prepend('<div class="error_comentario">'+data.mensaje+'</div>');
					}					
				},
				"json"
			).error(function() {
				$("#panel_comentarios_inside").prepend('<div class="error_comentario">No se pudo escribir el mensaje.</div>');
			});
			
		}
	}
	// Recarga los comentarios del partido seleccionado
	function recargarComentarios(idPartido) {
		totalCargados = 0;								// Resetea número de comentarios cargados
		if(timerComments) clearInterval(timerComments);	// Resetea el actualizador de comentarios
		cargarComentarios(idPartido);					// Vuelve a cargar comentarios
	}





