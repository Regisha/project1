/ *!
 * Datepicker для Bootstrap v1.8.0 (https://github.com/uxsolutions/bootstrap-datepicker)
 *
 * Лицензия на лицензию Apache v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 *
 * Изменения от Kartik Visweswaran (для поддержки Bootstrap 4.x)
 * /
(функция (заводская) {
    if (typeof define === 'function' && define.amd) {
        define (['jquery'], фабрика);
    } else if (typeof export === 'object') {
        завод (требуется ( 'JQuery'));
    } else {
        завод (Jquery);
    }
} (function ($, undefined) {
    функция UTCDate () {
        return new Date (Date.UTC.apply (Дата, аргументы));
    }

    функция UTCToday () {
        var today = new Date ();
        return UTCDate (today.getFullYear (), today.getMonth (), today.getDate ());
    }

    функция isUTCEquals (date1, date2) {
        вернуть (
            date1.getUTCFullYear () === date2.getUTCFullYear () &&
            date1.getUTCMonth () === date2.getUTCMonth () &&
            date1.getUTCDate () === date2.getUTCDate ()
        );
    }

    псевдоним функции (метод, deprecationMsg) {
        return function () {
            if (deprecationMsg! == undefined) {
                $ .Fn.datepicker.deprecated (deprecationMsg);
            }

            return this [method] .apply (это, аргументы);
        };
    }

    функция isValidDate (d) {
        return d &&! isNaN (d.getTime ());
    }

    var DateArray = (function () {
        var extras = {
            get: function (i) {
                return this.slice (i) [0];
            },
            содержит: function (d) {
                // Array.indexOf не является межсерверным;
                // $ .inArray не работает с датами
                var val = d && d.valueOf ();
                для (var i = 0, l = this.length; i <l; i ++)
                    // Используйте арифметику даты, чтобы дать датам с разными временами соответствовать
                    if (0 <= this [i] .valueOf () - val && this [i] .valueOf () - val <1000 * 60 * 60 * 24)
                        return i;
                return -1;
            },
            remove: function (i) {
                this.splice (i, 1);
            },
            replace: function (new_array) {
                if (! new_array)
                    вернуть;
                if (! $. isArray (new_array))
                    new_array = [new_array];
                this.clear ();
                this.push.apply (this, new_array);
            },
            clear: function () {
                this.length = 0;
            },
            copy: function () {
                var a = new DateArray ();
                a.replace (это);
                return a;
            }
        };

        return function () {
            var a = [];
            a.push.apply (a, arguments);
            $ .extend (a, дополнительные);
            return a;
        };
    }) ();


    // Объект Picker

    var Datepicker = function (element, options) {
        $ .data (элемент, 'datepicker', это);
        this._process_options (варианты);

        this.dates = new DateArray ();
        this.viewDate = this.o.defaultViewDate;
        this.focusDate = null;

        this.element = $ (element);
        this.isInput = this.element.is ('input');
        this.inputField = this.isInput? this.element: this.element.find ('input');
        this.component = this.element.hasClass ('date')? this.element.find ('. add-on, .input-group-addon, .kv-date-picker, .btn'): false;
        if (this.component && this.component.length === 0)
            this.component = false;
        this.isInline =! this.component && this.element.is ('div');

        this.picker = $ (DPGlobal.template);

        // Проверка шаблонов и вставка
        if (this._check_template (this.o.templates.leftArrow)) {
            this.picker.find ( 'пред') HTML (this.o.templates.leftArrow).
        }

        if (this._check_template (this.o.templates.rightArrow)) {
            . This.picker.find ( 'Далее') HTML (this.o.templates.rightArrow);
        }

        this._buildEvents ();
        this._attachEvents ();

        if (this.isInline) {
            this.picker.addClass ( 'DatePicker-рядный') appendTo (this.element).
        }
        else {
            this.picker.addClass ('datepicker-dropdown drop-menu');
        }

        if (this.o.rtl) {
            this.picker.addClass ( 'DatePicker-РТЛ');
        }

        if (this.o.calendarWeeks) {
            this.picker.find ('. datepicker-days .datepicker-switch, thead .datepicker-title, tfoot .today, tfoot .clear').
                .attr ('colspan', function (i, val) {
                    return Number (val) + 1;
                });
        }

        this._process_options ({
            startDate: this._o.startDate,
            endDate: this._o.endDate,
            daysOfWeekDisabled: this.o.daysOfWeekDisabled,
            daysOfWeekHighlighted: this.o.daysOfWeekHighlighted,
            dateDisabled: this.o.datesDisabled
        });

        this._allow_update = false;
        this.setViewMode (this.o.startView);
        this._allow_update = true;

        this.fillDow ();
        this.fillMonths ();

        this.update ();

        if (this.isInline) {
            Это шоу();
        }
    };

    Datepicker.prototype = {
        конструктор: Datepicker,

        _resolveViewName: function (view) {
            $ .each (DPGlobal.viewModes, function (i, viewMode) {
                if (view === i || $ .inArray (view, viewMode.names)! == -1) {
                    view = i;
                    return false;
                }
            });

            вид возврата;
        },

        _resolveDaysOfWeek: function (daysOfWeek) {
            if (! $. isArray (daysOfWeek))
                daysOfWeek = daysOfWeek.split (/ [, \ s] * /);
            return $ .map (daysOfWeek, Number);
        },

        _check_template: function (tmp) {
            пытаться {
                // Если пусто
                if (tmp === undefined || tmp === "") {
                    return false;
                }
                // Если нет html, все в порядке
                if ((tmp.match (/ [<>] / g) || []). length <= 0) {
                    return true;
                }
                // Проверка правильности html
                var jDom = $ (tmp);
                return jDom.length> 0;
            }
            catch (ex) {
                return false;
            }
        },

        _process_options: function (opts) {
            // Сохранять исходные параметры для справки
            this._o = $ .extend ({}, this._o, opts);
            // Обработанные параметры
            var o = this.o = $ .extend ({}, this._o);

            // Проверяем, доступна ли дата стиля «de-DE», если язык не должен
            // резервное копирование на двухбуквенный код, например, "de"
            var lang = o.language;
            if (! date [lang]) {
                lang = lang.split ('-') [0];
                если (! date [lang])
                    lang = defaults.language;
            }
            o.language = lang;

            // Получить индекс просмотра из любых псевдонимов
            o.startView = this._resolveViewName (o.startView);
            o.minViewMode = this._resolveViewName (o.minViewMode);
            o.maxViewMode = this._resolveViewName (o.maxViewMode);

            // Проверить вид между min и max
            o.startView = Math.max (this.o.minViewMode, Math.min (this.o.maxViewMode, o.startView));

            // true, false или Number> 0
            if (o.multidate! == true) {
                o.multidate = Number (o.multidate) || ложный;
                if (o.multidate! == false)
                    o.multidate = Math.max (0, o.multidate);
            }
            o.multidateSeparator = String (o.multidateSeparator);

            o.weekStart% = 7;
            o.weekEnd = (o.weekStart + 6)% 7;

            var format = DPGlobal.parseFormat (o.format);
            if (o.startDate! == -Infinity) {
                if (!! o.startDate) {
                    if (o.startDate instanceof Date)
                        o.startDate = this._local_to_utc (this._zero_time (o.startDate));
                    еще
                        o.startDate = DPGlobal.parseDate (o.startDate, формат, o.language, o.assumeNearbyYear);
                }
                else {
                    o.startDate = -Infinity;
                }
            }
            if (o.endDate! == Infinity) {
                if (!! o.endDate) {
                    if (o.endDate instanceof Date)
                        o.endDate = this._local_to_utc (this._zero_time (o.endDate));
                    еще
                        o.endDate = DPGlobal.parseDate (o.endDate, формат, o.language, o.assumeNearbyYear);
                }
                else {
                    o.endDate = Бесконечность;
                }
            }

            o.daysOfWeekDisabled = this._resolveDaysOfWeek (o.daysOfWeekDisabled || []);
            o.daysOfWeekHighlighted = this._resolveDaysOfWeek (o.daysOfWeekHighlighted || []);

            o.datesDisabled = o.datesDisabled || [];
            if (! $. isArray (o.datesDisabled)) {
                o.datesDisabled = o.datesDisabled.split (',');
            }
            o.datesDisabled = $ .map (o.datesDisabled, function (d) {
                return DPGlobal.parseDate (d, формат, o.language, o.assumeNearbyYear);
            });

            var plc = String (o.orientation) .toLowerCase (). split (/ \ s + / g),
                _plc = o.orientation.toLowerCase ();
            plc = $ .grep (plc, function (word) {
                return /^auto|left|right|top|bottom$/.test_wordword);
            });
            o.orientation = {x: 'auto', y: 'auto'};
            if (! _plc || _plc === 'auto')
                ; // бездействие
            else if (plc.length === 1) {
                switch (plc [0]) {
                    case 'top':
                    case 'bottom':
                        o.orientation.y = plc [0];
                        перерыв;
                    case 'left':
                    case 'right':
                        o.orientation.x = plc [0];
                        перерыв;
                }
            }
            else {
                _plc = $ .grep (plc, function (word) {
                    return /^left|right$ /.test_word);
                });
                o.orientation.x = _plc [0] || 'авто';

                _plc = $ .grep (plc, function (word) {
                    return /^top|bottom$/.test_wordword);
                });
                o.orientation.y = _plc [0] || 'авто';
            }
            if (o.defaultViewDate instanceof Date || typeof o.defaultViewDate === 'string') {
                o.defaultViewDate = DPGlobal.parseDate (o.defaultViewDate, формат, o.language, o.assumeNearbyYear);
            } else if (o.defaultViewDate) {
                var year = o.defaultViewDate.year || new Date (). getFullYear ();
                var month = o.defaultViewDate.month || 0;
                var day = o.defaultViewDate.day || 1;
                o.defaultViewDate = UTCDate (год, месяц, день);
            } else {
                o.defaultViewDate = UTCToday ();
            }
        },
        _События: [],
        _secondaryEvents: [],
        _applyEvents: function (evs) {
            для (var i = 0, el, ch, ev; i <evs.length; i ++) {
                el = evs [i] [0];
                if (evs [i] .length === 2) {
                    ch = undefined;
                    ev = evs [i] [1];
                } else if (evs [i] .length === 3) {
                    ch = evs [i] [1];
                    ev = evs [i] [2];
                }
                el.on (ev, ch);
            }
        },
        _unapplyEvents: function (evs) {
            для (var i = 0, el, ev, ch; i <evs.length; i ++) {
                el = evs [i] [0];
                if (evs [i] .length === 2) {
                    ch = undefined;
                    ev = evs [i] [1];
                } else if (evs [i] .length === 3) {
                    ch = evs [i] [1];
                    ev = evs [i] [2];
                }
                el.off (ev, ch);
            }
        },
        _buildEvents: function () {
            var events = {
                keyup: $ .proxy (function (e) {
                    if ($ .inArray (e.keyCode, [27, 37, 39, 38, 40, 32, 13, 9]) === -1)
                        this.update ();
                }, этот),
                keydown: $ .proxy (this.keydown, this),
                paste: $ .proxy (this.paste, this)
            };

            if (this.o.showOnFocus === true) {
                events.focus = $ .proxy (this.show, this);
            }

            if (this.isInput) {// один вход
                this._events = [
                    [this.element, events]
                ];
            }
            // компонент: вход + кнопка
            else if (this.component && this.inputField.length) {
                this._events = [
                    // Для компонентов, которые не являются readonly, разрешите клавиатуру nav
                    [this.inputField, события],
                    [this.component, {
                        click: $ .proxy (this.show, this)
                    }]
                ];
            }
            else {
                this._events = [
                    [this.element, {
                        щелкните: $ .proxy (this.show, this),
                        keydown: $ .proxy (this.keydown, this)
                    }]
                ];
            }
            this._events.push (
                // Компонент: прослушивание размытия на потомках элемента
                [this.element, '*', {
                    blur: $ .proxy (функция (e) {
                        this._focused_from = e.target;
                    }, этот)
                }],
                // Вход: прослушивание размытия на элементе
                [this.element, {
                    blur: $ .proxy (функция (e) {
                        this._focused_from = e.target;
                    }, этот)
                }]
            );

            if (this.o.immediateUpdates) {
                // Триггер вводит обновления сразу на измененный год / месяц
                this._events.push ([this.element, {
                    'changeYear changeMonth': $ .proxy (function (e) {
                        this.update (e.date);
                    }, этот)
                }]);
            }

            this._secondaryEvents = [
                [this.picker, {
                    щелкните: $ .proxy (this.click, this)
                }],
                [this.picker, '.prev, .next', {
                    щелкните: $ .proxy (this.navArrowsClick, this)
                }],
                [this.picker, '.day: not (.disabled)', {
                    click: $ .proxy (this.dayCellClick, this)
                }],
                [$ (окно), {
                    resize: $ .proxy (this.place, this)
                }],
                [$ (document), {
                    «mousedown touchstart»: $ .proxy (function (e) {
                        // Щелкнуть за пределами datepicker, скрыть его
                        если (!(
                            this.element.is (e.target) ||
                            this.element.find (e.target) .length ||
                            this.picker.is (e.target) ||
                            this.picker.find (e.target) .length ||
                            this.isInline
                        )) {
                            this.hide ();
                        }
                    }, этот)
                }]
            ];
        },
        _attachEvents: function () {
            this._detachEvents ();
            this._applyEvents (this._events);
        },
        _detachEvents: function () {
            this._unapplyEvents (this._events);
        },
        _attachSecondaryEvents: function () {
            this._detachSecondaryEvents ();
            this._applyEvents (this._secondaryEvents);
        },
        _detachSecondaryEvents: function () {
            this._unapplyEvents (this._secondaryEvents);
        },
        _trigger: function (event, altdate) {
            var date = altdate || this.dates.get (-1),
                local_date = this._utc_to_local (дата);

            this.element.trigger ({
                тип: событие,
                date: local_date,
                viewMode: this.viewMode,
                date: $ .map (this.dates, this._utc_to_local),
                format: $ .proxy (функция (ix, format) {
                    if (arguments.length === 0) {
                        ix = this.dates.length - 1;
                        format = this.o.format;
                    } else if (typeof ix === 'string') {
                        format = ix;
                        ix = this.dates.length - 1;
                    }
                    формат = формат || this.o.format;
                    var date = this.dates.get (ix);
                    return DPGlobal.formatDate (дата, формат, this.o.language);
                }, этот)
            });
        },

        show: function () {
            if (this.inputField.prop ('disabled') || (this.inputField.prop ('readonly') && this.o.enableOnReadonly === false))
                вернуть;
            if (! this.isInline)
                this.picker.appendTo (this.o.container);
            это место();
            this.picker.show ();
            this._attachSecondaryEvents ();
            this._trigger ( 'шоу');
            if ((window.navigator.msMaxTouchPoints || 'ontouchstart' в документе) && this.o.disableTouchKeyboard) {
                $ (This.element) .blur ();
            }
            верните это;
        },

        hide: function () {
            if (this.isInline ||! this.picker.is (': visible'))
                верните это;
            this.focusDate = null;
            this.picker.hide () отсоединить ().
            this._detachSecondaryEvents ();
            this.setViewMode (this.o.startView);

            if (this.o.forceParse && this.inputField.val ())
                this.setValue ();
            this._trigger ( 'скрыть');
            верните это;
        },

        destroy: function () {
            this.hide ();
            this._detachEvents ();
            this._detachSecondaryEvents ();
            this.picker.remove ();
            delete this.element.data (). datepicker;
            if (! this.isInput) {
                delete this.element.data (). date;
            }
            верните это;
        },

        paste: function (e) {
            var dateString;
            if (e.originalEvent.clipboardData && e.originalEvent.clipboardData.types
                && $ .inArray ('text / plain', e.originalEvent.clipboardData.types)! == -1) {
                dateString = e.originalEvent.clipboardData.getData ('text / plain');
            } else if (window.clipboardData) {
                dateString = window.clipboardData.getData ('Text');
            } else {
                вернуть;
            }
            this.setDate (DateString);
            this.update ();
            e.preventDefault ();
        },

        _utc_to_local: function (utc) {
            если (! utc) {
                return utc;
            }

            var local = new Date (utc.getTime () + (utc.getTimezoneOffset () * 60000));

            if (local.getTimezoneOffset ()! == utc.getTimezoneOffset ()) {
                local = new Date (utc.getTime () + (local.getTimezoneOffset () * 60000));
            }

            возвращение местного;
        },
        _local_to_utc: function (local) {
            return local && new Date (local.getTime () - (local.getTimezoneOffset () * 60000));
        },
        _zero_time: function (local) {
            return local && new Date (local.getFullYear (), local.getMonth (), local.getDate ());
        },
        _zero_utc_time: function (utc) {
            return utc && UTCDate (utc.getUTCFullYear (), utc.getUTCMonth (), utc.getUTCDate ());
        },

        getDates: function () {
            return $ .map (this.dates, this._utc_to_local);
        },

        getUTCDates: function () {
            return $ .map (this.dates, function (d) {
                вернуть новую дату (d);
            });
        },

        getDate: function () {
            return this._utc_to_local (this.getUTCDate ());
        },

        getUTCDate: function () {
            var selected_date = this.dates.get (-1);
            if (selected_date! == undefined) {
                вернуть новую дату (selected_date);
            } else {
                return null;
            }
        },

        clearDates: function () {
            this.inputField.val ( '');
            this.update ();
            this._trigger ( 'ChangeDate');

            if (this.o.autoclose) {
                this.hide ();
            }
        },

        setDates: function () {
            var args = $ .isArray (аргументы [0])? arguments [0]: аргументы;
            this.update.apply (это, args);
            this._trigger ( 'ChangeDate');
            this.setValue ();
            верните это;
        },

        setUTCDates: function () {
            var args = $ .isArray (аргументы [0])? arguments [0]: аргументы;
            this.setDates.apply (это, $ .map (args, this._utc_to_local));
            верните это;
        },

        setDate: псевдоним ('setDates'),
        setUTCDate: псевдоним ('setUTCDates'),
        remove: alias ('destroy', 'Method `remove` устарел и будет удален в версии 2.0. Используйте` destroy` вместо'),

        setValue: function () {
            var formatted = this.getFormattedDate ();
            this.inputField.val (отформатированные);
            верните это;
        },

        getFormattedDate: функция (формат) {
            if (format === undefined)
                format = this.o.format;

            var lang = this.o.language;
            return $ .map (this.dates, function (d) {
                return DPGlobal.formatDate (d, format, lang);
            .}) Присоединиться (this.o.multidateSeparator);
        },

        getStartDate: function () {
            return this.o.startDate;
        },

        setStartDate: function (startDate) {
            this._process_options ({startDate: startDate});
            this.update ();
            this.updateNavArrows ();
            верните это;
        },

        getEndDate: function () {
            return this.o.endDate;
        },

        setEndDate: function (endDate) {
            this._process_options ({endDate: endDate});
            this.update ();
            this.updateNavArrows ();
            верните это;
        },

        setDaysOfWeekDisabled: function (daysOfWeekDisabled) {
            this._process_options ({daysOfWeekDisabled: daysOfWeekDisabled});
            this.update ();
            верните это;
        },

        setDaysOfWeekHighlighted: function (daysOfWeekHighlighted) {
            this._process_options ({daysOfWeekHighlighted: daysOfWeekHighlighted});
            this.update ();
            верните это;
        },

        setDatesDisabled: функция (dateDisabled) {
            this._process_options ({dateDisabled: dateDisabled});
            this.update ();
            верните это;
        },

        место: function () {
            if (this.isInline)
                верните это;
            var calendarWidth = this.picker.outerWidth (),
                calendarHeight = this.picker.outerHeight (),
                visualPadding = 10,
                container = $ (this.o.container),
                windowWidth = container.width (),
                scrollTop = this.o.container === 'body'? $ (document) .scrollTop (): container.scrollTop (),
                appendOffset = container.offset ();

            var parentsZindex = [0];
            this.element.parents (). each (function () {
                var itemZIndex = $ (this) .css ('z-index');
                if (itemZIndex! == 'auto' && Number (itemZIndex)! == 0) parentsZindex.push (Number (itemZIndex));
            });
            var zIndex = Math.max.apply (Math, parentsZindex) + this.o.zIndexOffset;
            var offset = this.component? this.component.parent (). offset (): this.element.offset ();
            var height = this.component? this.component.outerHeight (true): this.element.outerHeight (false);
            var width = this.component? this.component.outerWidth (true): this.element.outerWidth (false);
            var left = offset.left - appendOffset.left;
            var top = offset.top - appendOffset.top;

            if (this.o.container! == 'body') {
                top + = scrollTop;
            }

            this.picker.removeClass (
                'datepicker-orient-top datepicker-orient-bottom' +
                'datepicker-orient-right datepicker-orient-left'
            );

            if (this.o.orientation.x! == 'auto') {
                this.picker.addClass ('datepicker-orient-' + this.o.orientation.x);
                if (this.o.orientation.x === 'right')
                    left - = calendarWidth - width;
            }
            // ориентация auto x лучше всего размещается: если она пересекает окно
            // край, подталкивать его вбок
            else {
                if (offset.left <0) {
                    // компонент находится за пределами окна с левой стороны. Переместите его в видимый диапазон
                    this.picker.addClass ( 'DatePicker-Orient-влево');
                    left - = offset.left - visualPadding;
                } else if (left + calendarWidth> windowWidth) {
                    // календарь передает правую границу вдовы. Совместите его с правой стороной компонента
                    this.picker.addClass ( 'DatePicker-Orient-право');
                    left + = width - calendarWidth;
                } else {
                    if (this.o.rtl) {
                        // По умолчанию вправо
                        this.picker.addClass ( 'DatePicker-Orient-право');
                    } else {
                        // По умолчанию слева
                        this.picker.addClass ( 'DatePicker-Orient-влево');
                    }
                }
            }

            // автоматическая ориентация y - лучшая ситуация: верхняя или нижняя, без подделки,
            // решение, основанное на том, что показывает больше календаря
            var yorient = this.o.orientation.y,
                top_overflow;
            if (yorient === 'auto') {
                top_overflow = -scrollTop + top - calendarHeight;
                yorient = top_overflow <0? 'снизу сверху';
            }

            this.picker.addClass ('datepicker-orient-' + yorient);
            if (yorient === 'top')
                top - = calendarHeight + parseInt (this.picker.css ('padding-top'));
            еще
                верх + высота;

            if (this.o.rtl) {
                var right = windowWidth - (left + width);
                this.picker.css ({
                    наверх: сверху,
                    верно-верно,
                    zIndex: zIndex
                });
            } else {
                this.picker.css ({
                    наверх: сверху,
                    слева: слева,
                    zIndex: zIndex
                });
            }
            верните это;
        },

        _allow_update: true,
        update: function () {
            if (! this._allow_update)
                верните это;

            var oldDates = this.dates.copy (),
                date = [],
                fromArgs = false;
            if (arguments.length) {
                $ .each (аргументы, $ .proxy (function (i, date) {
                    if (дата даты Date)
                        date = this._local_to_utc (дата);
                    dates.push (дата);
                }, этот));
                fromArgs = true;
            } else {
                date = this.isInput
                    ? this.element.val ()
                    : this.element.data ('date') || this.inputField.val ();
                if (date && this.o.multidate)
                    date = date.split (this.o.multidateSeparator);
                еще
                    даты = [даты];
                delete this.element.data (). date;
            }

            date = $ .map (даты, $ .proxy (функция (дата) {
                return DPGlobal.parseDate (дата, this.o.format, this.o.language, this.o.assumeNearbyYear);
            }, этот));
            date = $ .grep (date, $ .proxy (function (date) {
                вернуть (
                    ! this.dateWithinRange (date) ||
                    !Дата
                );
            }, это правда);
            this.dates.replace (даты);

            if (this.o.updateViewDate) {
                if (this.dates.length)
                    this.viewDate = new Date (this.dates.get (-1));
                else if (this.viewDate <this.o.startDate)
                    this.viewDate = новая дата (this.o.startDate);
                else if (this.viewDate> this.o.endDate)
                    this.viewDate = новая дата (this.o.endDate);
                еще
                    this.viewDate = this.o.defaultViewDate;
            }

            if (fromArgs) {
                // установка даты нажатием
                this.setValue ();
                this.element.change ();
            }
            else if (this.dates.length) {
                // установка даты путем ввода
                if (String (oldDates)! == String (this.dates) && fromArgs) {
                    this._trigger ( 'ChangeDate');
                    this.element.change ();
                }
            }
            if (! this.dates.length && oldDates.length) {
                this._trigger ( 'clearDate');
                this.element.change ();
            }

            this.fill ();
            верните это;
        },

        fillDow: function () {
            if (this.o.showWeekDays) {
                var dowCnt = this.o.weekStart,
                    html = '<tr>';
                if (this.o.calendarWeeks) {
                    html + = '<th class = "cw"> & # 160; </ th>';
                }
                while (dowCnt <this.o.weekStart + 7) {
                    html + = '<th class = "dow';
                    if ($ .inArray (dowCnt, this.o.daysOfWeekDisabled)! == -1)
                        html + = 'disabled';
                    html + = '">' + date [this.o.language] .daysMin [(dowCnt ++)% 7] + '</ th>';
                }
                html + = '</ tr>';
                this.picker.find ('. datepicker-days thead'). append (html);
            }
        },

        fillMonths: function () {
            var localDate = this._utc_to_local (this.viewDate);
            var html = '';
            var сфокусирован;
            для (var i = 0; i <12; i ++) {
                focus = localDate && localDate.getMonth () === i? «сосредоточено»: '';
                html + = '<span class = "month' + focus + '">' + date [this.o.language] .monthsShort [i] + '</ span>';
            }
            this.picker.find ('. datepicker-months td'). html (html);
        },

        setRange: function (range) {
            если (! range ||! range.length)
                удалите this.range;
            еще
                this.range = $ .map (диапазон, функция (d) {
                    return d.valueOf ();
                });
            this.fill ();
        },

        getClassNames: function (date) {
            var cls = [],
                year = this.viewDate.getUTCFullYear (),
                month = this.viewDate.getUTCMonth (),
                today = UTCToday ();
            if (date.getUTCFullYear () <year || (date.getUTCFullYear () === year && date.getUTCMonth () <месяц)) {
                cls.push ( 'старый');
            } else if (date.getUTCFullYear ()> year || (date.getUTCFullYear () === year && date.getUTCMonth ()> месяц)) {
                cls.push ( 'новый');
            }
            if (this.focusDate && date.valueOf () === this.focusDate.valueOf ())
                cls.push ( 'сосредоточены');
            // Сравните внутреннюю дату UTC с UTC сегодня, а не локально сегодня
            if (this.o.todayHighlight && isUTCEquals (дата, сегодня)) {
                cls.push ( 'сегодня');
            }
            if (this.dates.contains (date)! == -1)
                cls.push ( 'активный');
            if (! this.dateWithinRange (дата)) {
                cls.push ( 'отключено');
            }
            if (this.dateIsDisabled (дата)) {
                cls.push ('disabled', 'disabled-date');
            }
            if ($ .inArray (date.getUTCDay (), this.o.daysOfWeekHighlighted)! == -1) {
                cls.push ( 'выделены');
            }

            if (this.range) {
                if (date> this.range [0] && date <this.range [this.range.length - 1]) {
                    cls.push ( 'диапазон');
                }
                if ($ .inArray (date.valueOf (), this.range)! == -1) {
                    cls.push ( 'выбрано');
                }
                if (date.valueOf () === this.range [0]) {
                    cls.push ( 'Диапазон пуска');
                }
                if (date.valueOf () === this.range [this.range.length - 1]) {
                    cls.push ( 'Диапазон-конец');
                }
            }
            return cls;
        },

        _fill_yearsView: функция (селектор, cssClass, factor, year, startYear, endYear, beforeFn) {
            var html = '';
            var step = factor / 10;
            var view = this.picker.find (селектор);
            var startVal = Math.floor (year / factor) * factor;
            var endVal = startVal + step * 9;
            var focusVal = Math.floor (this.viewDate.getFullYear () / step) * step;
            var selected = $ .map (this.dates, function (d) {
                return Math.floor (d.getUTCFullYear () / step) * step;
            });

            var classes, tooltip, before;
            for (var currVal = startVal - step; currVal <= endVal + step; currVal + = step) {
                classes = [cssClass];
                tooltip = null;

                if (currVal === startVal - step) {
                    classes.push ( 'старый');
                } else if (currVal === endVal + step) {
                    classes.push ( 'новый');
                }
                if ($ .inArray (currVal, выбрано)! == -1) {
                    classes.push ( 'активный');
                }
                if (currVal <startYear || currVal> endYear) {
                    classes.push ( 'отключено');
                }
                if (currVal === focusVal) {
                    classes.push ( 'сосредоточены');
                }

                if (beforeFn! == $ .noop) {
                    before = beforeFn (новая дата (currVal, 0, 1));
                    if (до === undefined) {
                        before = {};
                    } else if (typeof before === 'boolean') {
                        before = {enabled: before};
                    } else if (typeof before === 'string') {
                        before = {classes: before};
                    }
                    if (before.enabled === false) {
                        classes.push ( 'отключено');
                    }
                    if (before.classes) {
                        classes = classes.concat (before.classes.split (/ \ s + /));
                    }
                    if (before.tooltip) {
                        tooltip = before.tooltip;
                    }
                }

                html + = '<span class = "' + classes.join ('') + '"' + (tooltip? 'title = "' + tooltip + '"': '') + '>' + currVal + '< / SPAN> ';
            }

            view.find ('. datepicker-switch'). text (startVal + '-' + endVal);
            view.find ( 'тд') HTML (HTML).
        },

        fill: function () {
            var d = new Date (this.viewDate),
                год = d.getUTCFullYear (),
                month = d.getUTCMonth (),
                startYear = this.o.startDate! == -Infinity? this.o.startDate.getUTCFullYear (): -Infinity,
                startMonth = this.o.startDate! == -Infinity? this.o.startDate.getUTCMonth (): -Infinity,
                endYear = this.o.endDate! == Бесконечность? this.o.endDate.getUTCFullYear (): Бесконечность,
                endMonth = this.o.endDate! == Бесконечность? this.o.endDate.getUTCMonth (): Бесконечность,
                todaytxt = даты [this.o.language]. Сегодня || даты ['en']. сегодня || '',
                cleartxt = даты [this.o.language] .clear || date ['en']. clear || '',
                titleFormat = даты [this.o.language] .titleFormat || даты [ 'ан']. форматирования заголовка,
                подсказка,
                до;
            if (isNaN (год) || isNaN (месяц))
                вернуть;
            this.picker.find ('. datepicker-days .datepicker-switch')
                .text (DPGlobal.formatDate (d, titleFormat, this.o.language));
            this.picker.find ('tfoot .today')
                .text (todaytxt)
                .css ('display', this.o.todayBtn === true || this.o.todayBtn === 'linked'? 'table-cell': 'none');
            this.picker.find ('tfoot .clear')
                .text (ClearTXT)
                .css ('display', this.o.clearBtn === true? 'table-cell': 'none');
            this.picker.find ('thead .datepicker-title')
                .text (this.o.title)
                .css ('display', typeof this.o.title === 'string' && this.o.title! == ''? 'table-cell': 'none');
            this.updateNavArrows ();
            this.fillMonths ();
            var prevMonth = UTCDate (год, месяц, 0),
                day = prevMonth.getUTCDate ();
            prevMonth.setUTCDate (день - (prevMonth.getUTCDay () - this.o.weekStart + 7)% 7);
            var nextMonth = новая дата (prevMonth);
            if (prevMonth.getUTCFullYear () <100) {
                nextMonth.setUTCFullYear (prevMonth.getUTCFullYear ());
            }
            nextMonth.setUTCDate (nextMonth.getUTCDate () + 42);
            nextMonth = nextMonth.valueOf ();
            var html = [];
            var weekDay, clsName;
            while (prevMonth.valueOf () <nextMonth) {
                weekDay = prevMonth.getUTCDay ();
                if (weekDay === this.o.weekStart) {
                    html.push ( '<TR>');
                    if (this.o.calendarWeeks) {
                        // ISO 8601: первая неделя содержит первый четверг.
                        // ИСО также объявляет о начале недели в понедельник, но мы можем быть более абстрактными здесь.
                        вар
                            // Начало текущей недели: на основе даты недели / текущей даты
                            ws = new Дата (+ prevMonth + (this.o.weekStart - weekDay - 7)% 7 * 864e5),
                            // Четверг этой недели
                            th = новая дата (число (ws) + (7 + 4 - ws.getUTCDay ())% 7 * 864e5),
                            // Первый четверг года, год с четверга
                            yth = new Date (Number (yth = UTCDate (th.getUTCFullYear (), 0, 1)) + (7 + 4 - yth.getUTCDay ())% 7 * 864e5),
                            // Календарная неделя: ms между днями, div ms в день, div 7 дней
                            calWeek = (th-yth) / 864e5 / 7 + 1;
                        html.push ('<td class = "cw">' + calWeek + '</ td>');
                    }
                }
                clsName = this.getClassNames (prevMonth);
                clsName.push ( 'день');

                var content = prevMonth.getUTCDate ();

                if (this.o.beforeShowDay! == $ .noop) {
                    before = this.o.beforeShowDay (this._utc_to_local (prevMonth));
                    if (до === undefined)
                        before = {};
                    else if (typeof before === 'boolean')
                        before = {enabled: before};
                    else if (typeof before === 'string')
                        before = {classes: before};
                    if (before.enabled === false)
                        clsName.push ( 'отключено');
                    если (до.классы)
                        clsName = clsName.concat (before.classes.split (/ \ s + /));
                    if (before.tooltip)
                        tooltip = before.tooltip;
                    if (before.content)
                        content = before.content;
                }

                // Проверяем, существует ли uniqueSort (поддерживается jquery> = 1.12 и> = 2.2)
                // Возвращение к уникальной функции для старых версий jQuery
                if ($ .isFunction ($. uniqueSort)) {
                    clsName = $ .uniqueSort (clsName);
                } else {
                    clsName = $ .unique (clsName);
                }

                html.push ('<td class = "' + clsName.join ('') + '"' + (tooltip? 'title = "' + tooltip + '"': '') + 'data-date = "' + prevMonth.getTime (). toString () + '">' + content + '</ td>');
                tooltip = null;
                if (weekDay === this.o.weekEnd) {
                    html.push ( '</ TR>');
                }
                prevMonth.setUTCDate (prevMonth.getUTCDate () + 1);
            }
            this.picker.find ('. datepicker-days tbody'). html (html.join (''));

            var monthsTitle = даты [this.o.language] .monthsTitle || date ['en']. monthsTitle || 'Месяцы';
            var months = this.picker.find ('. datepicker-months')
                .find ( 'DatePicker-переключатель')
                .text (this.o.maxViewMode <2? monthsTitle: year)
                .конец()
                .find ('tbody span'). removeClass ('active');

            $ .each (this.dates, function (i, d) {
                if (d.getUTCFullYear () === год)
                    . Months.eq (d.getUTCMonth ()) addClass ( 'активный');
            });

            if (year <startYear || year> endYear) {
                months.addClass ( 'отключено');
            }
            if (year === startYear) {
                months.slice (0, startMonth) .addClass ('disabled');
            }
            if (year === endYear) {
                months.slice (endMonth + 1) .addClass ('disabled');
            }

            if (this.o.beforeShowMonth! == $ .noop) {
                var that = this;
                $ .each (месяцы, функция (i, месяц) {
                    var moDate = новая дата (год, i, 1);
                    var before = that.o.beforeShowMonth (moDate);
                    if (до === undefined)
                        before = {};
                    else if (typeof before === 'boolean')
                        before = {enabled: before};
                    else if (typeof before === 'string')
                        before = {classes: before};
                    if (before.enabled === false &&! $ (месяц) .hasClass ('disabled'))
                        $ (Месяц) .addClass ( 'отключено');
                    если (до.классы)
                        $ (Месяц) .addClass (before.classes);
                    if (before.tooltip)
                        $ (месяц) .prop ('title', before.tooltip);
                });
            }

            // Создание декады / лет выбора
            this._fill_yearsView (
                ».datepicker лет,
                'год',
                10,
                год,
                StartYear,
                EndYear,
                this.o.beforeShowYear
            );

            // Создание сборщика столетий / десятилетий
            this._fill_yearsView (
                ».datepicker-десятилетия,
                «Десять лет»,
                100,
                год,
                StartYear,
                EndYear,
                this.o.beforeShowDecade
            );

            // Создание тысячелетия / веков
            this._fill_yearsView (
                ».datepicker-векам,
                «Век»,
                1000,
                год,
                StartYear,
                EndYear,
                this.o.beforeShowCentury
            );
        },

        updateNavArrows: function () {
            if (! this._allow_update)
                вернуть;

            var d = new Date (this.viewDate),
                год = d.getUTCFullYear (),
                month = d.getUTCMonth (),
                startYear = this.o.startDate! == -Infinity? this.o.startDate.getUTCFullYear (): -Infinity,
                startMonth = this.o.startDate! == -Infinity? this.o.startDate.getUTCMonth (): -Infinity,
                endYear = this.o.endDate! == Бесконечность? this.o.endDate.getUTCFullYear (): Бесконечность,
                endMonth = this.o.endDate! == Бесконечность? this.o.endDate.getUTCMonth (): Бесконечность,
                prevIsDisabled,
                nextIsDisabled,
                фактор = 1;
            switch (this.viewMode) {
                случай 4:
                    фактор * = 10;
                / * падает через * /
                случай 3:
                    фактор * = 10;
                / * падает через * /
                случай 2:
                    фактор * = 10;
                / * падает через * /
                Случай 1:
                    prevIsDisabled = Math.floor (year / factor) * factor <= startYear;
                    nextIsDisabled = Math.floor (year / factor) * factor + factor> endYear;
                    перерыв;
                случай 0:
                    prevIsDisabled = year <= startYear && month <= startMonth;
                    nextIsDisabled = year> = endYear && month> = endMonth;
                    перерыв;
            }

            this.picker.find ('. prev'). toggleClass ('disabled', prevIsDisabled);
            this.picker.find ('. next'). toggleClass ('disabled', nextIsDisabled);
        },

        click: function (e) {
            e.preventDefault ();
            e.stopPropagation ();

            var target, dir, day, year, month;
            target = $ (e.target);

            // Нажмите на переключатель
            if (target.hasClass ('datepicker-switch') && this.viewMode! == this.o.maxViewMode) {
                this.setViewMode (this.viewMode + 1);
            }

            // Нажмите на кнопку сегодня
            if (target.hasClass ('today') &&! target.hasClass ('день')) {
                this.setViewMode (0);
                this._setDate (UTCToday (), this.o.todayBtn === 'linked'? null: 'view');
            }

            // Нажмите на кнопку очистки
            if (target.hasClass ('clear')) {
                this.clearDates ();
            }

            if (! target.hasClass ('disabled')) {
                // Нажмите на месяц, год, десятилетие, столетие
                if (target.hasClass («месяц»)
                    || target.hasClass ( 'год')
                    || target.hasClass ( 'десятилетие')
                    || target.hasClass ('century')) {
                    this.viewDate.setUTCDate (1);

                    день = 1;
                    if (this.viewMode === 1) {
                        month = target.parent (). find ('span'). index (target);
                        year = this.viewDate.getUTCFullYear ();
                        this.viewDate.setUTCMonth (месяц);
                    } else {
                        месяц = ​​0;
                        year = Number (target.text ());
                        this.viewDate.setUTCFullYear (год);
                    }

                    this._trigger (DPGlobal.viewModes [this.viewMode - 1] .e, this.viewDate);

                    if (this.viewMode === this.o.minViewMode) {
                        this._setDate (UTCDate (год, месяц, день));
                    } else {
                        this.setViewMode (this.viewMode - 1);
                        this.fill ();
                    }
                }
            }

            if (this.picker.is (': visible') && this._focused_from) {
                this._focused_from.focus ();
            }
            удалите this._focused_from;
        },

        dayCellClick: функция (e) {
            var $ target = $ (e.currentTarget);
            var timestamp = $ target.data ('date');
            var date = новая дата (временная метка);

            if (this.o.updateViewDate) {
                if (date.getUTCFullYear ()! == this.viewDate.getUTCFullYear ()) {
                    this._trigger ('changeYear', this.viewDate);
                }

                if (date.getUTCMonth ()! == this.viewDate.getUTCMonth ()) {
                    this._trigger ('changeMonth', this.viewDate);
                }
            }
            this._setDate (дата);
        },

        // Нажмите на предыдущую или следующую
        navArrowsClick: function (e) {
            var $ target = $ (e.currentTarget);
            var dir = $ target.hasClass ('prev')? -1: 1;
            if (this.viewMode! == 0) {
                dir * = DPGlobal.viewModes [this.viewMode] .navStep * 12;
            }
            this.viewDate = this.moveMonth (this.viewDate, dir);
            this._trigger (DPGlobal.viewModes [this.viewMode] .e, this.viewDate);
            this.fill ();
        },

        _toggle_multidate: function (date) {
            var ix = this.dates.contains (date);
            if (! date) {
                this.dates.clear ();
            }

            if (ix! == -1) {
                if (this.o.multidate === true || this.o.multidate> 1 || this.o.toggleActive) {
                    this.dates.remove (IX);
                }
            } else if (this.o.multidate === false) {
                this.dates.clear ();
                this.dates.push (дата);
            }
            else {
                this.dates.push (дата);
            }

            if (typeof this.o.multidate === 'number')
                while (this.dates.length> this.o.multidate)
                    this.dates.remove (0);
        },

        _setDate: функция (дата, которая) {
            if (! which || which === 'date')
                this._toggle_multidate (date && new Дата (дата));
            if ((! which && this.o.updateViewDate) ||, который === 'view')
                this.viewDate = date && new Дата (дата);

            this.fill ();
            this.setValue ();
            if (! which || which! == 'view') {
                this._trigger ( 'ChangeDate');
            }
            this.inputField.trigger ( 'Изменение');
            if (this.o.autoclose && (! which || which === 'date')) {
                this.hide ();
            }
        },

        moveDay: function (date, dir) {
            var newDate = new Дата (дата);
            newDate.setUTCDate (date.getUTCDate () + dir);

            return newDate;
        },

        moveWeek: function (date, dir) {
            return this.moveDay (date, dir * 7);
        },

        moveMonth: function (date, dir) {
            if (! isValidDate (дата))
                return this.o.defaultViewDate;
            если (! dir)
                Дата возвращения;
            var new_date = new Дата (date.valueOf ()),
                day = new_date.getUTCDate (),
                month = new_date.getUTCMonth (),
                mag = Math.abs (dir),
                new_month, test;
            dir = dir> 0? 1: -1;
            if (mag === 1) {
                test = dir === -1
                    // Если вы вернетесь на месяц, убедитесь, что месяц не является текущим месяцем
                    // (например, 31 марта -> 31 февраля == 28 февраля, а не 02 марта)
                    ? function () {
                        return new_date.getUTCMonth () === month;
                    }
                    // Если вы продвигаетесь на один месяц, убедитесь, что месяц как ожидалось
                    // (например, 31 января -> 31 февраля == 28 февраля, а не 02 марта)
                    : function () {
                        return new_date.getUTCMonth ()! == new_month;
                    };
                new_month = month + dir;
                new_date.setUTCMonth (new_month);
                // Dec -> Jan (12) или Jan -> Dec (-1) - предел ожидаемой даты до 0-11
                new_month = (new_month + 12)% 12;
            }
            else {
                // Для величин> 1, перемещайте один месяц за раз ...
                для (var i = 0; i <mag; i ++)
                    // ..., что может уменьшить день (например, с 31 января по 28 февраля и т. д.) ...
                    new_date = this.moveMonth (new_date, dir);
                // ... затем сбросьте день, удерживая его в новом месяце
                new_month = new_date.getUTCMonth ();
                new_date.setUTCDate (день);
                test = function () {
                    return new_month! == new_date.getUTCMonth ();
                };
            }
            // Общий цикл сбрасывания даты - если дата находится вне конца месяца, сделайте это
            // конец месяца
            while (test ()) {
                new_date.setUTCDate (- день);
                new_date.setUTCMonth (new_month);
            }
            return new_date;
        },

        moveYear: function (date, dir) {
            return this.moveMonth (date, dir * 12);
        },

        moveAvailableDate: function (date, dir, fn) {
            делать {
                date = this [fn] (date, dir);

                if (! this.dateWithinRange (дата))
                    return false;

                fn = 'moveDay';
            }
            while (this.dateIsDisabled (дата));

            Дата возвращения;
        },

        weekOfDateIsDisabled: function (date) {
            return $ .inArray (date.getUTCDay (), this.o.daysOfWeekDisabled)! == -1;
        },

        dateIsDisabled: function (date) {
            вернуть (
                this.weekOfDateIsDisabled (date) ||
                $ .grep (this.o.datesDisabled, function (d) {
                    return isUTCEquals (дата, d);
                }). length> 0
            );
        },

        dateWithinRange: function (date) {
            return date> = this.o.startDate && date <= this.o.endDate;
        },

        keydown: функция (e) {
            if (! this.picker.is (': visible')) {
                if (e.keyCode === 40 || e.keyCode === 27) {// разрешить повторное показ выбора
                    Это шоу();
                    e.stopPropagation ();
                }
                вернуть;
            }
            var dateChanged = false,
                dir, newViewDate,
                focusDate = this.focusDate || this.viewDate;
            switch (e.keyCode) {
                случай 27: // побег
                    if (this.focusDate) {
                        this.focusDate = null;
                        this.viewDate = this.dates.get (-1) || this.viewDate;
                        this.fill ();
                    }
                    еще
                        this.hide ();
                    e.preventDefault ();
                    e.stopPropagation ();
                    перерыв;
                дело 37: // осталось
                дело 38: // вверх
                дело 39: // право
                дело 40: // вниз
                    если (! this.o.keyboardNavigation || this.o.daysOfWeekDisabled.length === 7)
                        перерыв;
                    dir = e.keyCode === 37 || e.keyCode === 38? -1: 1;
                    if (this.viewMode === 0) {
                        if (e.ctrlKey) {
                            newViewDate = this.moveAvailableDate (focusDate, dir, 'moveYear');

                            if (newViewDate)
                                this._trigger ('changeYear', this.viewDate);
                        } else if (e.shiftKey) {
                            newViewDate = this.moveAvailableDate (focusDate, dir, 'moveMonth');

                            if (newViewDate)
                                this._trigger ('changeMonth', this.viewDate);
                        } else if (e.keyCode === 37 || e.keyCode === 39) {
                            newViewDate = this.moveAvailableDate (focusDate, dir, 'moveDay');
                        } else if (! this.weekOfDateIsDisabled (focusDate)) {
                            newViewDate = this.moveAvailableDate (focusDate, dir, 'moveWeek');
                        }
                    } else if (this.viewMode === 1) {
                        if (e.keyCode === 38 || e.keyCode === 40) {
                            dir = dir * 4;
                        }
                        newViewDate = this.moveAvailableDate (focusDate, dir, 'moveMonth');
                    } else if (this.viewMode === 2) {
                        if (e.keyCode === 38 || e.keyCode === 40) {
                            dir = dir * 4;
                        }
                        newViewDate = this.moveAvailableDate (focusDate, dir, 'moveYear');
                    }
                    if (newViewDate) {
                        this.focusDate = this.viewDate = newViewDate;
                        this.setValue ();
                        this.fill ();
                        e.preventDefault ();
                    }
                    перерыв;
                случай 13: // введите
                    если (! this.o.forceParse)
                        перерыв;
                    focusDate = this.focusDate || this.dates.get (-1) || this.viewDate;
                    if (this.o.keyboardNavigation) {
                        this._toggle_multidate (focusDate);
                        dateChanged = true;
                    }
                    this.focusDate = null;
                    this.viewDate = this.dates.get (-1) || this.viewDate;
                    this.setValue ();
                    this.fill ();
                    if (this.picker.is (': visible')) {
                        e.preventDefault ();
                        e.stopPropagation ();
                        если (this.o.autoclose)
                            this.hide ();
                    }
                    перерыв;
                case 9: // tab
                    this.focusDate = null;
                    this.viewDate = this.dates.get (-1) || this.viewDate;
                    this.fill ();
                    this.hide ();
                    перерыв;
            }
            if (dateChanged) {
                if (this.dates.length)
                    this._trigger ( 'ChangeDate');
                еще
                    this._trigger ( 'clearDate');
                this.inputField.trigger ( 'Изменение');
            }
        },

        setViewMode: function (viewMode) {
            this.viewMode = viewMode;
            this.picker
                .children ( 'DIV')
                .скрывать()
                .filter ('. datepicker-' + DPGlobal.viewModes [this.viewMode] .clsName)
                .шоу();
            this.updateNavArrows ();
            this._trigger ('changeViewMode', новая дата (this.viewDate));
        }
    };

    var DateRangePicker = функция (элемент, параметры) {
        $ .data (элемент, 'datepicker', это);
        this.element = $ (element);
        this.inputs = $ .map (options.inputs, function (i) {
            вернуть i.jquery? i [0]: i;
        });
        delete options.inputs;

        this.keepEmptyValues ​​= options.keepEmptyValues;
        delete options.keepEmptyValues;

        datepickerPlugin.call ($ (this.inputs), опции)
            .on ('changeDate', $ .proxy (this.dateUpdated, this));

        this.pickers = $ .map (this.inputs, function (i) {
            return $ .data (i, 'datepicker');
        });
        this.updateDates ();
    };
    DateRangePicker.prototype = {
        updateDates: function () {
            this.dates = $ .map (this.pickers, function (i) {
                return i.getUTCDate ();
            });
            this.updateRanges ();
        },
        updateRanges: function () {
            var range = $ .map (this.dates, function (d) {
                return d.valueOf ();
            });
            $ .each (this.pickers, function (i, p) {
                p.setRange (диапазон);
            });
        },
        clearDates: function () {
            $ .each (this.pickers, function (i, p) {
                p.clearDates ();
            });
        },
        dateUpdated: функция (e) {
            // `this.updating` является обходным путем для предотвращения бесконечной рекурсии
            // между вызовом `changeDate` и вызовом` setUTCDate`. До тех пор
            // есть лучший механизм.
            если (это.обновление)
                вернуть;
            this.updating = true;

            var dp = $ .data (e.target, 'datepicker');

            if (dp === undefined) {
                вернуть;
            }

            var new_date = dp.getUTCDate (),
                keep_empty_values ​​= this.keepEmptyValues,
                i = $ .inArray (e.target, this.inputs),
                j = i - 1,
                k = i + 1,
                l = this.inputs.length;
            if (i === -1)
                вернуть;

            $ .each (this.pickers, function (i, p) {
                if (! p.getUTCDate () && (p === dp ||! keep_empty_values))
                    p.setUTCDate (NEW_DATE);
            });

            if (new_date <this.dates [j]) {
                // Дата перемещается ранее / слева
                while (j> = 0 && new_date <this.dates [j]) {
                    this.pickers [J -] setUTCDate (NEW_DATE);.
                }
            } else if (new_date> this.dates [k]) {
                // Дата перемещается позже / справа
                while (k <l && new_date> this.dates [k]) {
                    this.pickers [K ++] setUTCDate (NEW_DATE).
                }
            }
            this.updateDates ();

            удалить this.updating;
        },
        destroy: function () {
            $ .map (this.pickers, function (p) {
                p.destroy ();
            });
            $ (this.inputs) .off ('changeDate', this.dateUpdated);
            delete this.element.data (). datepicker;
        },
        remove: alias ('destroy', 'Method `remove` устарел и будет удален в версии 2.0. Используйте` destroy` вместо')
    };

    функция opts_from_el (el, prefix) {
        // Вывод параметров из элемента данных-attrs
        var data = $ (el) .data (),
            out = {}, inkey,
            replace = new RegExp ('^' + prefix.toLowerCase () + '([AZ])');
        prefix = new RegExp ('^' + prefix.toLowerCase ());

        функция re_lower (_, a) {
            return a.toLowerCase ();
        }

        для (var key in data)
            if (prefix.test (ключ)) {
                inkey = key.replace (заменить, re_lower);
                out [inkey] = данные [ключ];
            }
        возвращение;
    }

    функция opts_from_locale (lang) {
        // Вывод параметров из локальных плагинов
        var out = {};
        // Проверяем, доступна ли дата стиля «de-DE», если язык не должен
        // резервное копирование на двухбуквенный код, например, "de"
        if (! date [lang]) {
            lang = lang.split ('-') [0];
            если (! date [lang])
                вернуть;
        }
        var d = даты [lang];
        $ .each (locale_opts, function (i, k) {
            если (k в d)
                out [k] = d [k];
        });
        возвращение;
    }

    var old = $ .fn.datepicker;
    var datepickerPlugin = функция (опция) {
        var args = Array.apply (null, arguments);
        args.shift ();
        var internal_return;
        this.each (function () {
            var $ this = $ (this),
                data = $ this.data ('datepicker'),
                options = typeof option === 'object' && option;
            если (! data) {
                var elopts = opts_from_el (это, 'дата'),
                    // Предварительные условия
                    xopts = $ .extend ({}, defaults, elopts, options),
                    locopts = opts_from_locale (xopts.language),
                    // Приоритет опций: js args, data-attrs, locales, defaults
                    opts = $ .extend ({}, defaults, locopts, elopts, options);
                if ($ this.hasClass ('input-daterange') || opts.inputs) {
                    $ .extend (opts, {
                        Входы: opts.inputs || $ This.find ( 'вход'). ToArray ()
                    });
                    data = new DateRangePicker (это, выбор);
                }
                else {
                    data = new Datepicker (this, opts);
                }
                $ this.data ('datepicker', data);
            }
            if (typeof option === 'string' && typeof data [option] === 'function') {
                internal_return = data [option] .apply (данные, args);
            }
        });

        если (
            internal_return === undefined ||
            internal_return instanceof Datepicker ||
            internal_return instanceof DateRangePicker
        )
            верните это;

        if (this.length> 1)
            throw new Error ('Использование разрешено только для коллекции одного элемента (' + option + 'function)');
        еще
            return internal_return;
    };
    $ .fn.datepicker = datepickerPlugin;

    var defaults = $ .fn.datepicker.defaults = {
        guessNearbyYear: false,
        autoclose: false,
        beforeShowDay: $ .noop,
        beforeShowMonth: $ .noop,
        beforeShowYear: $ .noop,
        beforeShowDecade: $ .noop,
        beforeShowCentury: $ .noop,
        calendarWeeks: false,
        clearBtn: false,
        toggleActive: false,
        daysOfWeekDisabled: [],
        daysOfWeekHighlighted: [],
        dateDisabled: [],
        endDate: Бесконечность,
        forceParse: true,
        формат: «мм / дд / гггг»,
        keepEmptyValues: false,
        keyboardNavigation: true,
        язык: 'en',
        minViewMode: 0,
        maxViewMode: 4,
        multidate: false,
        multidateSeparator: ',',
        ориентация: «авто»,
        rtl: false,
        startDate: -Infinity,
        startView: 0,
        todayBtn: false,
        todayHighlight: false,
        updateViewDate: true,
        weekStart: 0,
        disableTouchKeyboard: false,
        enableOnReadonly: true,
        showOnFocus: true,
        zIndexOffset: 10,
        контейнер: «тело»,
        directUpdates: false,
        заглавие: '',
        шаблоны: {
            leftArrow: '& # x00AB;',
            rightArrow: '& # x00BB;'
        },
        showWeekDays: true
    };
    var locale_opts = $ .fn.datepicker.locale_opts = [
        'формат',
        «РТЛ»,
        'WeekStart'
    ];
    $ .fn.datepicker.Constructor = Datepicker;
    var date = $ .fn.datepicker.dates = {
        ru: {
            дней: [«воскресенье», «понедельник», «вторник», «среда», «четверг», «пятница», «суббота»]
            daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
            daysMin: [ «Su», «Mo», «Tu», «We», «Th», «Fr», «Sa»]
            месяцев: «Январь», «Февраль», «Марш», «Апрель», «Май», «Июнь», «Июль», «Август», «Сентябрь», «Октябрь», «Ноябрь», ],
            monthShort: [«Jan», «Feb», «Mar», «Apr», «May», «Jun», «Jul», «Aug», «Sep», «Oct», «Nov», «Dec», ],
            сегодня: «Сегодня»,
            ясно: «Очистить»,
            titleFormat: "MM yyyy"
        }
    };

    var DPGlobal = {
        viewModes: [
            {
                имена: ['days', 'month'],
                clsName: «дни»,
                e: 'changeMonth'
            },
            {
                имена: ['months', 'year'],
                clsName: «месяцы»,
                e: 'changeYear',
                navStep: 1
            },
            {
                имена: ['years', 'ten'],
                clsName: 'years',
                e: 'changeDecade',
                navStep: 10
            },
            {
                имена: [«десятилетия», «столетие»]
                clsName: «десятилетия»,
                e: 'changeCentury',
                navStep: 100
            },
            {
                имена: ['столетия "," тысячелетие "],
                clsName: «столетия»,
                e: «changeMillennium»,
                navStep: 1000
            }
        ],
        validParts: / dd? | DD? | mm? | MM? | yy (?: yy)? / g,
        непустота: / [^ - \ /: - @ \ u5e74 \ u6708 \ u65e5 \ [- `{- ~ \ t \ n \ r] + / g,
        parseFormat: function (format) {
            if (typeof format.toValue === 'function' && typeof format.toDisplay === 'function')
                формат возврата;
            // IE обрабатывает \ 0 как конец строки в входах (обрезает значение),
            // так что это плохим разделителем формата, так или иначе
            var separators = format.replace (this.validParts, '\ 0'). split ('\ 0'),
                parts = format.match (this.validParts);
            if (! separators ||! separators.length ||! parts || parts.length === 0) {
                throw new Error («Недопустимый формат даты»);
            }
            return {separators: separators, parts: parts};
        },
        parseDate: функция (дата, формат, язык, acceptNearby) {
            if (! date)
                return undefined;
            if (дата даты Date)
                Дата возвращения;
            if (typeof format === 'string')
                format = DPGlobal.parseFormat (формат);
            if (format.toValue)
                return format.toValue (дата, формат, язык);
            var fn_map = {
                    d: 'moveDay',
                    m: 'moveMonth',
                    w: 'moveWeek',
                    y: 'moveYear'
                },
                dateAliases = {
                    вчера: '-1d',
                    сегодня: '+ 0d',
                    завтра: '+ 1d'
                },
                части, часть, dir, i, fn;
            if (date in dateAliases) {
                date = dateAliases [дата];
            }
            if (/^[\-+]\d+[dmwy]([\s,]+[\-+]\d+[dmwy])*$/i.test(date)) {
                parts = date.match (/ ([\ - +] \ d +) ([dmwy]) / gi);
                date = new Date ();
                для (i = 0; i <parts.length; i ++) {
                    part = parts [i] .match (/ ([\ - +] \ d +) ([dmwy]) / i);
                    dir = Number (часть [1]);
                    fn = fn_map [часть [2] .toLowerCase ()];
                    date = Datepicker.prototype [fn] (date, dir);
                }
                return Datepicker.prototype._zero_utc_time (дата);
            }

            parts = date && date.match (this.nonpunctuation) || [];

            функция applyNearbyYear (год, порог) {
                if (threshold === true)
                    порог = 10;

                // если год составляет 2 цифры или меньше, чем пользователь, скорее всего, пытается получить недавнее столетие
                если (год <100) {
                    год + = 2000;
                    // если новый год превышает пороговые годы, используйте последний век
                    if (year> ((новая дата ()). getFullYear () + порог)) {
                        год - = 100;
                    }
                }

                год возврата;
            }

            var parsed = {},
                seters_order = ['yyyy', 'yy', 'M', 'MM', 'm', 'mm', 'd', 'dd'],
                setters_map = {
                    yyyy: function (d, v) {
                        return d.setUTCFullYear (acceptNearby? applyNearbyYear (v, acceptNearby): v);
                    },
                    m: функция (d, v) {
                        if (isNaN (d))
                            return d;
                        v - = 1;
                        тогда как (v <0) v + = 12;
                        v% = 12;
                        d.setUTCMonth (v);
                        while (d.getUTCMonth ()! == v)
                            d.setUTCDate (d.getUTCDate () - 1);
                        return d;
                    },
                    d: функция (d, v) {
                        return d.setUTCDate (v);
                    }
                },
                val, фильтруют;
            setters_map ['yy'] = setters_map ['yyyy'];
            seters_map ['M'] = setters_map ['MM'] = setters_map ['mm'] = setters_map ['m'];
            setters_map ['dd'] = setters_map ['d'];
            date = UTCToday ();
            var fparts = format.parts.slice ();
            // Удалить ненужные части
            if (parts.length! == fparts.length) {
                fparts = $ (fparts) .filter (function (i, p) {
                    return $ .inArray (p, setters_order)! == -1;
                }) ToArray ().
            }

            // Остаток процесса
            function match_part () {
                var m = this.slice (0, parts [i] .length),
                    p = части [i] .slice (0, m.length);
                return m.toLowerCase () === p.toLowerCase ();
            }

            if (parts.length === fparts.length) {
                var cnt;
                для (i = 0, cnt = fparts.length; i <cnt; i ++) {
                    val = parseInt (части [i], 10);
                    part = fparts [i];
                    if (isNaN (val)) {
                        переключатель (часть) {
                            case 'MM':
                                filter = $ (даты [язык] .months) .filter (match_part);
                                val = $ .inArray (отфильтрован [0], даты [язык] .months) + 1;
                                перерыв;
                            case 'M':
                                filter = $ (даты [язык] .monthsShort) .filter (match_part);
                                val = $ .inArray (фильтр [0], даты [язык] .monthsShort) + 1;
                                перерыв;
                        }
                    }
                    проанализировано [part] = val;
                }
                var _date, s;
                для (i = 0; i <setters_order.length; i ++) {
                    s = seters_order [i];
                    if (s в parsed &&! isNaN (проанализировано [s])) {
                        _date = новая дата (дата);
                        setters_map [s] (_ дата, разобран [s]);
                        если (! isNaN (_date))
                            date = _date;
                    }
                }
            }
            Дата возвращения;
        },
        formatDate: функция (дата, формат, язык) {
            if (! date)
                вернуть '';
            if (typeof format === 'string')
                format = DPGlobal.parseFormat (формат);
            if (format.toDisplay)
                return format.toDisplay (дата, формат, язык);
            var val = {
                d: date.getUTCDate (),
                D: даты [язык] .daysShort [date.getUTCDay ()],
                DD: даты [язык] .days [date.getUTCDay ()],
                m: date.getUTCMonth () + 1,
                M: даты [язык] .monthsShort [date.getUTCMonth ()],
                MM: даты [язык] .months [date.getUTCMonth ()],
                yy: date.getUTCFullYear (). toString (). substring (2),
                yyyy: date.getUTCFullYear ()
            };
            val.dd = (val.d <10? '0': '') + val.d;
            val.mm = (val.m <10? '0': '') + val.m;
            date = [];
            var seps = $ .extend ([], format.separators);
            for (var i = 0, cnt = format.parts.length; i <= cnt; i ++) {
                если (seps.length)
                    date.push (seps.shift ());
                date.push (Val [format.parts [I]]);
            }
            return date.join ('');
        },
        headTemplate: '<thead>' +
        '<tr>' +
        '<th colspan = "7" class = "datepicker-title"> </ th>' +
        '</ tr>' +
        '<tr>' +
        '<th class = "prev">' + defaults.templates.leftArrow + '</ th>' +
        '<th colspan = "5" class = "datepicker-switch"> </ th>' +
        '<th class = "next">' + defaults.templates.rightArrow + '</ th>' +
        '</ tr>' +
        </ THEAD> ',
        contTemplate: '<tbody> <tr> <td colspan = "7"> </ td> </ tr> </ tbody>',
        footTemplate: '<tfoot>' +
        '<tr>' +
        '<th colspan = "7" class = "today"> </ th>' +
        '</ tr>' +
        '<tr>' +
        '<th colspan = "7" class = "clear"> </ th>' +
        '</ tr>' +
        </ TFOOT> '
    };
    DPGlobal.template = '<div class = "datepicker">' +
        '<div class = "datepicker-days">' +
        '<table class = "table-condensed">' +
        DPGlobal.headTemplate +
        '<tbody> </ tbody>' +
        DPGlobal.footTemplate +
        '</ table>' +
        '</ div>' +
        '<div class = "datepicker-months">' +
        '<table class = "table-condensed">' +
        DPGlobal.headTemplate +
        DPGlobal.contTemplate +
        DPGlobal.footTemplate +
        '</ table>' +
        '</ div>' +
        '<div class = "datepicker-years">' +
        '<table class = "table-condensed">' +
        DPGlobal.headTemplate +
        DPGlobal.contTemplate +
        DPGlobal.footTemplate +
        '</ table>' +
        '</ div>' +
        '<div class = "datepicker-ten">' +
        '<table class = "table-condensed">' +
        DPGlobal.headTemplate +
        DPGlobal.contTemplate +
        DPGlobal.footTemplate +
        '</ table>' +
        '</ div>' +
        '<div class = "datepicker-century">' +
        '<table class = "table-condensed">' +
        DPGlobal.headTemplate +
        DPGlobal.contTemplate +
        DPGlobal.footTemplate +
        '</ table>' +
        '</ div>' +
        </ DIV> ';

    $ .fn.datepicker.DPGlobal = DPGlobal;


    / * DATEPICKER NO CONFLICT
    * =================== * *

    $ .fn.datepicker.noConflict = function () {
        $ .fn.datepicker = old;
        верните это;
    };

    / * ВЕРСИЯ DATEPICKER
     * =================== * *
    $ .fn.datepicker.version = '1.8.0';

    $ .fn.datepicker.deprecated = function (msg) {
        var console = window.console;
        if (console && console.warn) {
            console.warn ('DEPRECATED:' + msg);
        }
    };


    / * DATEPICKER DATA-API
    * ================== * *

    $ (Документ) .он (
        'focus.datepicker.data-api click.datepicker.data-api',
        '[Данные обеспечивают = "DatePicker"]',
        функция (e) {
            var $ this = $ (this);
            if ($ this.data ('datepicker'))
                вернуть;
            e.preventDefault ();
            // щелчок компонента требует, чтобы мы явно показывали его
            datepickerPlugin.call ($ this, 'show');
        }
    );
    $ (функция () {
        datepickerPlugin.call ($ ( '[данные обеспечивают = "DatePicker-инлайн"]'));
    });

}));
