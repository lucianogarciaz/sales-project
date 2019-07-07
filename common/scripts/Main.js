var Main = {
    
    opciones: {
        pace: {
            ajax: {
                trackMethods: ['GET', 'POST']
            }
        },
        tooltip: {
            container: 'body',
            title: function () {
                return $(this).data('hint');
            }
        },
        confirm: {
            'title': function () {
                return $(this).data('mensaje');
            },
            'rootSelector': $('body'),
            'btnOkLabel': 'Aceptar',
            'btnCancelLabel': 'Cancelar',
            'singleton': true,
            'placement': 'left',
            'container': 'body'

        }
    },
    selectores: {
        tooltip: '[data-hint]',
        confirm: '[data-mensaje]', // Confirmar acción
        selectOnFocus: '[data-focus-select]', // Seleccionar el texto al hacer focus
        modal: '[data-modal]',
        ajax: '[data-ajax]', // Request a la url indicada en este selector
        focusInit: '[data-focus-init]', //Inicializar el foco en un input determinado de un formulario
        focusTo: '[data-focus-to]', //Pone el foco en el input especificado luego de apretar enter | tab
    },
    init: function () {
        var _this = Main;
        window.paceOptions = _this.opciones.pace;

        // Inicialización de directivas de Vue
        VueDirectives.init();
        // Selecciono como activo en el sidebar la página actual
        $('.sidebar-menu').find('a').each(function () {
            if ($(this).attr('href') != '/' && window.location.pathname.indexOf($(this).attr('href')) == 0)
            {
                $(this).closest('.treeview').addClass('active');
                $(this).closest('li').addClass('active');
            }
        });

        // Selecciono como activo en el sidebar la página actual
        $('.nav-tabs').find('a').each(function () {
            if ($(this).attr('href') != '/' && $(this).attr('href').indexOf(window.location.pathname) == 0)
                $(this).closest('li').addClass('active');
        });

        // Oculto los menús que no tienen nada activo
        $('.sidebar-menu').find('.treeview-menu').each(function () {
            if ($(this).find('li').length == 0)
                $(this).closest('.treeview').hide();
        });

        // Oculto los menús que no tienen nada activo en navbars dentro del content (usado en permisos)
        $('.content .navbar-nav').find('.dropdown').each(function () {
            if ($(this).find('li').length == 0)
                $(this).hide();
        });

        // Affix
        var $affixElement = $('div[data-spy="affix"]');
        $affixElement.width($affixElement.parent().width());
        $('body').on('affix.bs.affix', 'div[data-spy="affix"]', function (e) {
            var $this = $(this);
            if ($this.outerHeight() + 250 >= Math.max($(document).height(), $(document.body).height()))
            {
                e.preventDefault();
            }
        });

        // Reemplazar el % por %%%% en select2
        $('body').on('keyup', 'input.select2-search__field', function (e) {
            var $this = $(this);
            if ($this.val() == '%' && e.which == 53)
            {
                $this.val('%%%%');
                $this.trigger('input');
            }
        });

        _this.initAjax();
        _this.initEventos();
        // _this.inputmask();
    },
    initEventos: function () {
        var _this = Main;
        $(document).ready(function () {
            //Inicio eventos de focus

            $(_this.selectores.focusInit).focus();
            //Para inicar foco en una ventana modal
            $('body').on('shown.bs.modal', function () {
                $(Main.selectores.focusInit).focus();
            })

            setTimeout(function () {
                _this.initAjax();
            }, 300)

        });

        $('body').on('click', _this.selectores.selectOnFocus, function (event) {
            $(event.target).select();
        });

        $('body').on('click', _this.selectores.modal, function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            _this.modal($(this).data('modal'));
        });

        // Hacer request con ajax en los elementos con data-ajax=url. Evita recarga y muestra mensaje de éxito si hay data-success
        $('body').on('click', _this.selectores.ajax, function (e) {
            _this.ajax(this);
        });

        $('body').on('keydown', _this.selectores.focusTo, function (e) {
            _this = this;
            //Funcion recursiva que devuelve el siguiente target enabled con focus-to. 
            siguienteTarget = function (target) {
                if (!$(target).prop('disabled')) {
                    return target;
                } else {
                    siguiente = '#' + $(target).data('focus-to');
                    return siguienteTarget(siguiente);
                }
            }

            anteriorTarget = function (target) {
                if (!$('#' + target).prop('disabled')) {
                    return '#' + target;
                } else {
                    anterior = $('body').find("[data-focus-to='" + target + "']").prop('id');
                    return anteriorTarget(anterior);
                }
            }

            moverTarget = function (target, tipo) {

                switch (tipo) {
                    case 'INPUT' :
                        $(target).focus().select();
                        break;
                    case 'SELECT' :
                        //Muevo el foco según sea dropdownlist o select2
                        //Si es select2: 
                        if ($(target).hasClass("select2-hidden-accessible")) {
                            //Muevo el foco e inicio un evento para detectar la seleccion en select2 y al apretar teclas
                            siguiente = siguienteTarget('#' + $(target).data('focus-to'));
                            tipoInputSiguiente = $(siguiente).prop('nodeName');
                            $(target).select2('open');
                            $(target).on('select2:select', function () {
                                //console.log(siguienteTarget, tipoInputSiguienteTarget);
                                moverTarget(siguiente, tipoInputSiguiente);
                            })
                            $('.select2-search__field').on('keydown', function (e) {
                                if (!e.shiftKey && e.which == 9) {
                                    moverTarget(siguiente, tipoInputSiguiente);
                                }
                                if (e.shiftKey && e.which == 9) {
                                    idActual = $(target).prop('id');
                                    anterior = $('body').find("[data-focus-to='" + idActual + "']").prop('id');
                                    targetAnterior = anteriorTarget(anterior);
                                    tipoInputAnterior = $(targetAnterior).prop('nodeName');
                                    e.preventDefault();
                                    moverTarget(targetAnterior, tipoInputAnterior)
                                }

                            })
                        } else {
                            $(target).focus().select();
                        }
                        break;
                    case 'TEXTAREA':
                        $(target).focus().select();
                        break;
                    default:
                        $(target).focus().select();
                        break;
                }

            }

            //Muevo el foco al siguiente input según su tipo. Si está apretado el tab vuelvo el foco al input anterior
            if (e.shiftKey && e.which == 9) {
                idActual = $(_this).prop('id');
                anterior = $('body').find("[data-focus-to='" + idActual + "']").prop('id');
                targetAnterior = anteriorTarget(anterior);
                tipoInputAnterior = $(targetAnterior).prop('nodeName');
                e.preventDefault();
                moverTarget(targetAnterior, tipoInputAnterior)
            }

            tipoInputActual = $(_this).prop('nodeName');
            target = '#' + $(_this).data('focus-to');
            siguiente = siguienteTarget(target);
            tipoInputSiguiente = $(siguiente).prop('nodeName');

            switch (tipoInputActual) {
                //Input normal
                case 'INPUT' :
                    if (!e.shiftKey && (e.which == 13 || e.which == 9)) {
                        e.preventDefault();
                        moverTarget(siguiente, tipoInputSiguiente)
                    }
                    break;
                    //Input tipo dropdwonlist o select2
                case 'SELECT':
                    $(_this).on('change', function () {
                        moverTarget(siguiente, tipoInputSiguiente);
                    })
                    if (!e.shiftKey && (e.which == 9)) {
                        moverTarget(siguiente, tipoInputSiguiente);
                    }

                    // }
                    break;
                case 'TEXTAREA':
                    if (!e.shiftKey && (e.which == 13 || e.which == 9)) {
                        e.preventDefault();
                        moverTarget(siguiente, tipoInputSiguiente)
                    }
                    break;
                default:
                    if (!e.shiftKey && (e.which == 13 || e.which == 9)) {
                        moverTarget(siguiente, tipoInputSiguiente)
                    }
                    break;
                    //code block
            }

        });


    },
    initAjax: function () {
        var _this = Main;
        $('.tooltip').remove();

        $(_this.selectores.tooltip).tooltip(_this.opciones.tooltip);
        // Mensaje pidiendo confirmación en las acciones con data-mensaje
        $(_this.selectores.confirm).confirmation(_this.opciones.confirm);

        //  Sortable.init(); 
    },
    inputmask: function () {
        Inputmask.extendAliases({
            'moneda': {
                alias: 'numeric',
                groupSeparator: '.',
                autoGroup: '!0',
                digits: 2,
                radixPoint: ',',
                autoUnmask: true,
                unmaskAsNumber: true,
                removeMaskOnSubmit: true,
                onBeforeMask: function (value) {
                    var processedValue = value.replace(/\./g, ",");
                    return processedValue;
                }
            }
        });
    },
    modal: function (url) {
        var _this = Main;

        // Si hay un modal abierto no abro otro
        if ($('.modal').length > 0)
            return;

        var html = '<div class="modal fade"></div>';

        $(html).modal({
            backdrop: 'static',
            keyboard: false})
                .on('hidden.bs.modal', function () {
                    $(this).remove();
                })
                .load(url, function () {
                    var $modal = $(this);
                    var $form = $(this).find('form');

                    setTimeout(function () {
                        $modal.trigger('shown.bs.modal');

                        // Obtengo el primer input no oculto
                        var $primerInput = $form.find('input:not([type=hidden]),select').filter(':first');

                        // Hago focus si es type=text o select y no es un datepicker
                        if ($primerInput.hasClass('select2-hidden-accessible'))
                            $primerInput.select2('open');
                        else if (($primerInput.attr('type') == 'text' || $primerInput.is('select')) &&
                                // es datepicker
                                !$primerInput.hasClass('datepicker-to') && !$primerInput.hasClass('datepicker-from') && !$primerInput.parent().hasClass('date'))
                            $primerInput.focus();

                        _this.initAjax();

                    }, 500);
                    $modal.on('beforeSubmit', 'form', function (e) {
                        _this.submitModal(this);
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        return false;
                    });
                });
    },
    // Evento ejecutado para hacer submit de formularios con ajax
    submitModal: function (form) {
        var $form = $(form);

        // Se usa FormData porque permite también la subida de archivos
        var datos = new FormData(form);

        //Desactivo el botón de submit, para que el usuario no realice clicks 
        //repetidos hasta que se reciba la respuesta del servidor.
        $form.closest('.modal-content').find('[data-dismiss=modal]').attr('disabled', true);

        // Sólo si no está deshabilitado el botón de submit sigo adelante. Para prevenir doble submit 
        // (pasaba hasta que se deshabilitaba el botón)
        if (!$form.find(':submit').attr('disabled'))
        {
            $form.find(':submit').attr('disabled', true);

            //Se realiza el request con los datos por POST        
            $.ajax({
                url: $form.attr("action"),
                data: datos,
                type: 'POST',
                contentType: false,
                processData: false, })
                    .done(function (data) {
                        if (data.error)
                        {
                            var evento = jQuery.Event("error.modalform");
                            $('.modal').trigger(evento, [data]);

                            if (!evento.isDefaultPrevented())
                            {
                                var mensaje = data.error;
                                var tipo = 'danger';
                                //Agregando mensaje de error al diálogo modal
                                var html = '<div id="mensaje-modal" class="alert alert-' + tipo + ' alert-dismissable">'
                                        + '<i class="fa fa-ban"></i>'
                                        + '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'
                                        + '<b class="texto" >' + mensaje + '</b>'
                                        + '</div>';
                                $('#errores-modal').html(html);
                            }

                            //Se activa nuevamente el botón
                            $form.closest('.modal-content').find('[data-dismiss=modal]').attr('disabled', false);
                            $form.find(':submit').attr('disabled', false);
                        } else
                        {
                            var evento = jQuery.Event("success.modalform");
                            $('.modal').trigger(evento, [data]);

                            if (!evento.isDefaultPrevented())
                            {
                                if ($form.closest(".modal-dialog").data('no-reload') === undefined)
                                    location.reload();
                                else
                                    $('.modal').modal('hide');
                            } else
                            {
                                //Se activa nuevamente el botón
                                $form.closest('.modal-content').find('[data-dismiss=modal]').attr('disabled', false);
                                $form.find(':submit').attr('disabled', false);
                            }
                        }
                    })
                    .fail(function () {
                        var tipo = 'danger';
                        var mensaje = 'Error en la comunicación con el servidor. Contacte con el administrador.';
                        //Agregando mensaje de error al diálogo modal
                        var html = '<div id="mensaje-modal" class="alert alert-' + tipo + ' alert-dismissable">'
                                + '<i class="fa fa-ban"></i>'
                                + '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'
                                + '<b class="texto" >' + mensaje + '</b>'
                                + '</div>';
                        $('#errores-modal').html(html);
                        //Se activa nuevamente el botón
                        $form.closest('.modal-content').find('[data-dismiss=modal]').attr('disabled', false);
                        $form.find(':submit').attr('disabled', false);
                    });
        }
    },
    // Hacer request con ajax en los elementos con data-ajax=url. Evita recarga y muestra mensaje de éxito si hay data-success
    ajax: function (elemento) {
        var url = $(elemento).data('ajax');
        var success = $(elemento).data('success');

        $.get(url)
                .done(function (data) {
                    if (data.error)
                    {
                        var evento = jQuery.Event("error.ajax");
                        $(elemento).trigger(evento, [data]);

                        if (!evento.isDefaultPrevented())
                        {
                            var tipo = 'danger';
                            var mensaje = data.error;
                            var icono = 'ban';
                            //Agregando mensaje de error al diálogo modal
                            var html = '<div id="mensaje-modal" class="alert alert-' + tipo + ' alert-dismissable">'
                                    + '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'
                                    + '<i class="fa fa-' + icono + '"></i>'
                                    + '<b class="texto" >' + mensaje + '</b>'
                                    + '</div>';
                            $('#errores').html(html);
                        }
                    } else
                    {
                        if (success)
                        {
                            var tipo = 'success';
                            var mensaje = success;
                            var icono = 'check';
                            //Agregando mensaje de error al diálogo modal
                            var html = '<div id="mensaje-modal" class="alert alert-' + tipo + ' alert-dismissable">'
                                    + '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'
                                    + '<i class="fa fa-' + icono + '"></i>'
                                    + '<b class="texto" >' + mensaje + '</b>'
                                    + '</div>';
                            $('#errores').html(html);
                        } else
                        {
                            var evento = jQuery.Event("success.ajax");
                            $(elemento).trigger(evento, [data]);

                            if (!evento.isDefaultPrevented())
                            {
                                if ($.support.pjax)
                                {
                                    window.location.hash = '';
                                    $.pjax.reload('#pjax-container');
                                } else
                                    location.reload();
                            }
                        }
                    }
                })
                .fail(function () {
                    var tipo = 'danger';
                    var mensaje = 'Error en la comunicación con el servidor. Contacte con el administrador.';
                    var icono = 'ban';
                    //Agregando mensaje de error al diálogo modal
                    var html = '<div id="mensaje-modal" class="alert alert-' + tipo + ' alert-dismissable">'
                            + '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'
                            + '<i class="fa fa-' + icono + '"></i>'
                            + '<b class="texto" >' + mensaje + '</b>'
                            + '</div>';
                    $('#errores').html(html);
                });
    },
    // Sonido de error
    errorBeep: function () {
        var snd = new Audio("/sound/error.ogg");
        snd.play();
    },
    nudgeBeep: function () {
        var snd = new Audio("/sound/nudge.ogg");
        snd.play();
    },
    warningBeep: function () {
        var snd = new Audio("/sound/warning.oga");
        snd.play();
    },
    imprimir: function () {
        var colapsado = $("body").hasClass('sidebar-collapse');

        if (!colapsado)
            $("body").addClass('sidebar-collapse');

        window.print();

        if (!colapsado)
            $("body").removeClass('sidebar-collapse');
    }
};

$(function () {
    Main.init();
});
