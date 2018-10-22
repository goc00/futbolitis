/* 	Autor: Gastón Orellana C.
 *	Fecha: 22-11-2011
 *	Declaración de componentes / llamados de JQuery para el manejo de
 *	login en futbolitis. Se gatilla luego de la carga del DOM de la página */

    $(document).ready (function() {
		
		// Caja de noticias
		var segPause = 10;
		$('#caja_news').vTicker({
			speed: 500,
			pause: segPause*1000,
			showItems: 2,
			animation: 'fade',
			mousePause: true,
			height: 0,
			direction: 'up'
		});
		
		$('#panel_comentarios_inside').vTicker({
			speed: 500,
			pause: segPause*1000,
			showItems: 2,
			animation: 'fade',
			mousePause: true,
			height: 0,
			direction: 'up'
		});
		
		// Accordion del sitio principal
		$(function() {
			$("#accordion").accordion();
		});
		$("#accordion").accordion("option", "header", 'h3');
		
		// Controla todos los select del formulario de preinscripción y registro
		$("#pre_form, #registro_form select").change(function() {
		
			// Datos relacionados al select
			var nomObj = $(this).attr("name");
			var valObj = $(this).val();
			var index = $("select").index(this);
			var tabla = "";
			var div = "";
			var datos = "";
			var control = true;
		
			switch(nomObj) {
				case "region_cmb":
					tabla = "provincia";
					div = "";
					break;
				case "provincia_cmb":
					tabla = "comuna";
					break;
				default:
					control = false;
					break;
			}
			
			// Flag para hacer nada cuando se selecciona el último combo
			if (control) {
				// DIV al que se le cargarán los datos
				div = "#"+tabla+"_cmb";
				
				$.post(
					sitio+"registro/cargarCombos/",
					{
						"tabla": tabla,
						"valor" : valObj
					},
					function(data) {
						// NO CONSIDERA al select de tipo de dirección (casa o departamento) y el de celulares
						if(data.exito == "si") {
							// Remuevo todos los option de los combos posteriores al actual
							$('#pre_form, #registro_form select:not(#tipo_dire_txt, #dig_cel_txt):gt('+index+') option').remove();
							
							// Agrego la opción por defecto
							$('#pre_form, #registro_form select:not(#tipo_dire_txt, #dig_cel_txt):gt('+index+')').html($('<option value="" selected="selected">Selecciona tu opci&oacute;n</option>'));
							
							// Genera los option dinámicamente						
							for(var i=0; i<data.datosParaLlenar.length; i++) {
								var objX = data.datosParaLlenar[i];
								var nomCol = tabla+"_nombre";
								var nomId = tabla+"_id";
								
								nomId = nomId.toUpperCase();
								nomId = "objX."+nomId;
								nomCol = nomCol.toUpperCase();
								nomCol = "objX."+nomCol;
								
								datos += '<option value="'+eval(nomId)+'">'+eval(nomCol)+'</option>';
							}
				
							// CARGA LOS DATOS
							$(div).append(datos);	
							
						} else if(data.exito == "no") {
							// Mensaje de error
							alert(data.mensaje);
						} else {
							// Si llega acá, es porque se seleccionó la opción por defecto y sin valor
							// exito == "neutro"
							$('#pre_form, #registro_form select:not(#tipo_dire_txt, #dig_cel_txt):gt('+index+') option').remove();
							$('#pre_form, #registro_form select:not(#tipo_dire_txt, #dig_cel_txt):gt('+index+')').html($('<option value="" selected="selected">Selecciona tu opci&oacute;n</option>'));
						}
					},
					"json"
				).error(function() {
					alert("Error obteniendo los datos. Si el error persiste, contáctate con el Administrador.");
				});
			}
			
		});
		
		
		/* ******************************************
         * PRE-INSCRIPCIÓN
         * ****************************************** */
		$("#pre_form").submit(function() {
			$.post(
                $(this).attr("action"),
                {	
					"hash": $("#hash").val(),
					
					"nombre_txt": $("#nombre_txt").val(),
					"ape_pat_txt": $("#ape_pat_txt").val(),
					"ape_mat_txt": $("#ape_mat_txt").val(),
					"pais_cmb": $("#pais_cmb").val(),
					"region_cmb": $("#region_cmb").val(),
					"provincia_cmb": $("#provincia_cmb").val(),
					"comuna_cmb": $("#comuna_cmb").val(),
					
					"dire_txt": $("#dire_txt").val(),
					"tipo_dire_txt": $("#tipo_dire_txt").val(),
					"num_dire_txt": $("#num_dire_txt").val(),
					
					"cod_tel_txt": $("#cod_tel_txt").val(),
					"dig_tel_txt": $("#dig_tel_txt").val(),
					"tel_txt": $("#tel_txt").val(),
					
					"cod_cel_txt": $("#cod_cel_txt").val(),
					"dig_cel_txt": $("#dig_cel_txt").val(),
					"cel_txt": $("#cel_txt").val()
				},
				function(data) {
					var msg = $('<div title="Futbolitis">'+data.mensaje+'</div>');
					msg.dialog({
						modal: false,
						buttons: [
							{
								text: "Ok",
								click: function() {
									// Cierra el dialog
									$(this).dialog('close');
									if(data.exito == "si") window.location.href = sitio+'principal';
								}
							}
						]
					});
				},
				"json"
			);
			
			return false;
		});
		 
		 
		
		
		
		/* ******************************************
         * LOGIN
         * ******************************************
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
							// Verifica el tipo de autentificación
							// N: normal, P: pre-inscripción, I:inicial
							if(data.tipo_login == "P") {
								// Obliga a tener que completar los datos antes de poder ingresar al juego
								// RECORDAR que la pre-inscripción tiene toda la información por defecto
								
								var preIns = '<div title="¡Bienvenido!">';
								preIns += '¡Bienvenido a Futbolitis!<br />';
								preIns += 'Felicitaciones por haberte pre-inscrito, ahora posees 30 créditos de regalo que podrás utilizar sobre las opciones plus para aumentar tus chances de ganar.<br />';
								preIns += 'Antes de continuar, debes completar tus datos y poder así a ganar inmediatamente.';
								preIns += '</div>';
								
								$(preIns).dialog({
									modal: true,
									buttons: [
										{
											text: "Completar inscripción",
											click: function() {
												// Lanza la página para completar la información del usuario
												window.location.href = 'preinscripcion/go/'+data.id_usuario;
												$(this).dialog('close');
											}
										}
									]
								});
								
							} else if(data.tipo_login == "I") {
								// Solo despliega un mensaje de bienvenida
								var cont = '<div title="¡Bienvenido!">';
								cont += '¡Bienvenido a Futbolitis!<br />';
								cont += 'Antes de comenzar a jugar y ganar, puedes pasar por las instrucciones... blablabla';
								cont += '</div>';

								$(cont).dialog({
									modal: true,
									buttons: [
										{
											text: "¡A jugar!",
											click: function() {
												window.location.href = 'principal';
												$(this).dialog('close');
											}
										}
									]
								});
								
							} else {
								// Llama al controlador inicial, a la interfaz de juego
								window.location.href = 'principal';
							}
						}
					},
					"json"
                ).error(function() {
					alert("Error al intentar autentificarse en el sistema");
				});
				
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
					
					"pais_cmb": $("#pais_cmb").val(),
					"region_cmb": $("#region_cmb").val(),
					"provincia_cmb": $("#provincia_cmb").val(),
					"comuna_cmb": $("#comuna_cmb").val(),
					
					"dire_txt": $("#dire_txt").val(),
					"tipo_dire_txt": $("#tipo_dire_txt").val(),
					"num_dire_txt": $("#num_dire_txt").val(),
					
					"cod_tel_txt": $("#cod_tel_txt").val(),
					"dig_tel_txt": $("#dig_tel_txt").val(),
					"tel_txt": $("#tel_txt").val(),
					
					"cod_cel_txt": $("#cod_cel_txt").val(),
					"dig_cel_txt": $("#dig_cel_txt").val(),
					"cel_txt": $("#cel_txt").val()
                },
                function(data) {
					if(data.exito == "si") {
						mostrarDialog(data.mensaje, sitio);
					} else {
						mostrarDialog(data.mensaje, "");
					}
				  },
                  "json"
                ).error(function() {
					alert("Ha sucedido un error en el envío de los datos");
                });

            // Previene el submit standard del navegador y la navegación de páginas
            return false;
        });

		
		/* ******************************************** 
		 * RECUPERAR CONTRASEñA
		 * ******************************************** */
		 $("#form_rec_pass").submit(function() {
			$.post(
                $(this).attr("action"),
                {
                    "nom_re_usuario_txt": $("#nom_re_usuario_txt").val(),
                    "pass_ant_txt": $("#pass_ant_txt").val(),
                    "pass_new_txt": $("#pass_new_txt").val(),
                    "pass_re_new_txt": $("#pass_re_new_txt").val()
                },
                function(data) {
					if(data.exito == "si") {
						mostrarDialog(data.mensaje, sitio);
					} else {
						mostrarDialog(data.mensaje, "");
					}
				  },
                  "json"
                ).error(function() {
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
			
			// Obtiene todos los input del tipo text
			var inputs = $("#form_apostar :input[type=text]");
			
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
		

		
		// Despliega mensaje para el botón de apostar
		//$("#apostar_btn").tooltip();
		
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
			
			// ID partido anterior
			var idPartidoAnterior = $("#numero_partido").val();
			
			$.post(
				sitio+"principal/cargarPartido/",
				{
					"idPartido" : idPartido,
					"idPartidoAnterior" : idPartidoAnterior
				},
				function(data) {
				
					var arrApuestas = data.apuestas;
					var horaServidor = data.horaServidor;
					var comentarios = data.comentarios;
					var objPartido = data.objPartido;
					var objPartidoAnterior = data.objPartidoAnterior;
					
					// Cronómetro
					if(timer) clearInterval(timer);
					cronometro2(horaServidor, objPartido.fecha_programada, objPartido.hora_programada);
					
					// Elimina fila del listado
					$('#partido_'+objPartido.id_partido).remove();
						
					// Modifica los valores con respecto al partido seleccionado
					// IMPORTANTE, controla el id_partido que estará disponible para apostar
					
					$("#numero_partido").val(objPartido.id_partido);	
					
					$("#nombre_partido_local").html(objPartido.nom_equipo_local);
					$("#partido_lugar").html(objPartido.lugar);
					$("#partido_fecha_programada").html(objPartido.fecha_programada);
					$("#partido_hora_programada").html(objPartido.hora_programada);
					$("#nombre_partido_visita").html(objPartido.nom_equipo_visita);
						
					// Título del partido
					$("#es_destacado").html(objPartido.titulo);
						
					// Agrega la nueva fila con el id_partido antiguo
					var tr =	'<tr id="partido_'+objPartidoAnterior.id_partido+'" onMouseOver=\'destacarPartido(this, true)\' onMouseOut=\'destacarPartido(this, false)\' onClick=\'cargarPartido3('+objPartidoAnterior.id_partido+')\''+
									'height="25" bgColor="#FFFFFF">'+
									'<td>'+objPartidoAnterior.nom_equipo_local+'</td>'+
									'<td>v/s</td>'+
									'<td>'+objPartidoAnterior.nom_equipo_visita+'</td>'+
									'<td>'+
										'<div>'+objPartidoAnterior.fechaFormat+'</div>'+
										'<div>'+objPartidoAnterior.horaFormat+'</div>'+
									'</td>'+
								'</tr>';
					$("#listado_partidos_disponibles").append(tr);

					// Limpia todos los inputs
					var inputs = $("#form_apostar :input[type=text]");
					inputs.each(function() { $(this).val(""); });
					
					// Si encuentra apuestas, las despliega en los input correspondientes
					for(var i=0; i<arrApuestas.length; i++) {
						var ap = arrApuestas[i];
						$("#opcion-A-"+ap.id_opcion).val(ap.resultado_local); 
						$("#opcion-B-"+ap.id_opcion).val(ap.resultado_visita);
					}
					
					// Muestra los comentarios del partido
					var totalComentarios = comentarios.length;
					
					if(totalComentarios > 0) {
						var outputComentarios = '<div id="panel_comentarios_inside"><ul>';
						for(var i=0; i<totalComentarios; i++) {
							var objComentario = comentarios[i];
							outputComentarios += '<li>'+
													'<span class="com_nombre_usuario">'+objComentario.nombre_usuario+'</span>:&nbsp;'+
													'<span class="com_mensaje">'+objComentario.mensaje+'</span><br />'+objComentario.fecha+'</span>';
												 '</li>';
						}
						outputComentarios += '</ul></div>';
						$("#panel_comentarios").html(outputComentarios);

						var segPause = 10;
						$('#panel_comentarios_inside').vTicker({
							speed: 500,
							pause: segPause*1000,
							showItems: 2,
							animation: 'fade',
							mousePause: true,
							height: 0,
							direction: 'up'
						});
					} else {
						$("#panel_comentarios").html('<div class="no_comentario">No hay comentarios para este partido. ¡Deja el tuyo y se el primero!</div>');
					}
					
					// Libera el bloqueo
					bloqueo = false;
				},
				"json"
			).error(function() {
				alert("No se han podido cargar los datos");
			});
		}
		
	}
	
	function cronometro2(fechaServidor, fecha, hora) {
		// Va a buscar la hora al servidor
		var arrHoraServidor = fechaServidor.split(" ");
		var fechaX = arrHoraServidor[0].split("-");
		var horaX = arrHoraServidor[1].split(":");
		
		var elemento = "#estado_partido";
		var arrFecha = fecha.split("-");
		var arrHora = hora.split(":");
		
		// año, mes, dia, hora, minuto, segundos, milisegundos
		var tiempoPartido = new Date(arrFecha[0], arrFecha[1]-1, arrFecha[2], arrHora[0], arrHora[1], arrHora[2], 0);
		var tiempoActual = new Date(fechaX[0], fechaX[1]-1, fechaX[2], horaX[0], horaX[1], horaX[2], 0);
		
		// Resta y obtiene el resultado en milisegundos, luego lo redondea
		var diferencia = tiempoPartido.getTime() - tiempoActual.getTime();
		diferencia = Math.floor(diferencia/1000)*1000;
		
		if(diferencia > 0) {
			// Muestra un mensaje mientras aparece el cronómetro
			$(elemento).html("Cargando...").fadeIn('slow');
			
			// Timer GENERAL
			timer = setInterval(function() {
				//var fechaCronometro = new Date(0,0,0,0,0,0,diferencia);  // milisegundos
				
				if(diferencia <= 0) {
					clearInterval(timer); //timer.stop();
					$(elemento).html("Cerrado");
				} else {
					$(elemento).html(interpretaFecha(tiempoPartido, false));
				}
				
				diferencia -= 1000;	// le resta 1 segundo
			}, 1000);

		} else {
			// Está cerrado el timer
			$(elemento).html("Cerrado");
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
	
	
	// Almacena el comentario escrito por el jugador
	function escribirMensaje() {
		var mensaje = $.trim($("#caja_mensaje").val());
		var idPartido = $("#numero_partido").val();

		// Verifica que no esté dentro del intervalo de control de flooding
		$.post(
			sitio+"comentario/escribirComentario/",
			{
				"caja_mensaje": mensaje,
				"id_partido": idPartido
			},
			function(data) {
				mostrarDialog(data.mensaje, "");				
			},
			"json"
		).error(function() {
			alert("ERROR: No se pudieron cargar los datos. Si el error persiste, por favor contáctate con el Administrador.");
		});
	}


	// MUESTRA RESULTADOS DE LOS PARTIDOS
	function verResultados(obj) {
		var nombre = $(obj).attr('id');
		var arrNombre = nombre.split("_");
		var idPartido = arrNombre[arrNombre.length-1];
		
		// Envía el ID por post para traer los datos
		$.post(
				sitio+"pasado/muestraResultados/",
				{
					"id_partido": idPartido
				},
				function(data) {
					if(data.exito == "si") {
						var numResultados = data.devolucion.length;
						
						if(numResultados > 0) {
							// Muestra los resultados en un JDialog
							// Arma la salida
							var salida = '';
							salida += '<div title="Resultados partido">';
							salida += '<table align="center">';
							salida += '<tr>';
							salida += '<td align="center">L</td>';
							salida += '<td align="center">-</td>';
							salida += '<td align="center">V</td>';
							salida += '</tr>';
							for(var i=0; i<numResultados; i++) {
								
								var objResultado = data.devolucion[i];
								
								salida += '<tr>';
									salida += '<td>'+objResultado.resultado_local+'</td>';
									salida += '<td>'+objResultado.nombre_opcion+'</td>';
									salida += '<td>'+objResultado.resultado_visita+'</td>';
								salida += '</tr>';
							}
							salida += '</table>';
							salida += '<div>';
							
							$(salida).dialog({
								modal: false,
								buttons: [
									{
										text: "Ok",
										click: function() { $(this).dialog('close'); }
									}
								]
							});
							
						} else {
							alert("No existen resultados para este partido");
						}
					} else {
						alert(data.devolucion);
					}					
				},
				"json"
			).error(function() {
				alert('No se pudieron cargar los resultados para el partido seleccionado');
			});
	}
	
	// Función para desplegar mensajes a través de Dialog
	function mostrarDialog(cont, ir) {
		var msg = $('<div title="Futbolitis">'+cont+'</div>');
		msg.dialog({
			modal: false,
			buttons: [
				{
					text: "Ok",
					click: function() {
						// Cierra el dialog
						$(this).dialog('close');
						if(ir != "") window.location.href = ir;
					}
				}
			]
		});
	}
	
	
	/* ****************************************
	 * PARA COMPRAR BOLSAS DE CRÉDITOS
	 * **************************************** */
	function comprar(hash) {
		$.post(
			sitio+"comprar/buy/",
			{
				"hash" : hash
			},
			function(data) {
				if(data.exito == "si") {
					mostrarDialog(data.mensaje, "");
					// Actualiza contenido con el total
				} else {
					mostrarDialog(data.mensaje, "");
				}				
			},
			"json"
		).error(function() {
			alert('No se pudieron enviar los datos');
		});
	}
	
	/* ****************************************
	 * DESPLEGAR NOTICIA COMPLETA
	 * **************************************** */
	function masInfo(idNoticia) {
		$.post(
			sitio+"noticia/masInfo/",
			{
				"id_noticia" : idNoticia
			},
			function(data) {
				if(data.exito == "si") {
					// Levanta la información del objeto noticia
					var objNoticia = data.resultado;
					var output = '<div><b>'+objNoticia.titulo+'</b></div>';
					output += '<div>';
					output += '<div style="position:relative; float: left;">'+objNoticia.fecha+'</div>';
					output += '<div style="position:relative; float: right;">'+objNoticia.veces_leida+' veces leída</div>';
					output += '</div>';
					output += '<p style="text-align:left; float: left;">'+objNoticia.contenido+'</p>';
					mostrarDialog(output, "");
				} else {
					mostrarDialog(data.mensaje, "");
				}				
			},
			"json"
		).error(function() {
			alert('No se pudieron enviar los datos');
		});
	}