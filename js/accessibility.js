/*
 Highcharts JS v9.2.2 (2021-08-24)

 Accessibility module

 (c) 2010-2021 Highsoft AS
 Author: Oystein Moseng

 License: www.highcharts.com/license
*/
'use strict';
(function (b) {
    "object" === typeof module && module.exports ? (b["default"] = b, module.exports = b) : "function" === typeof define && define.amd ? define("highcharts/modules/accessibility", ["highcharts"], function (v) {
        b(v);
        b.Highcharts = v;
        return b
    }) : b("undefined" !== typeof Highcharts ? Highcharts : void 0)
})(function (b) {
    function v(b, e, q, n) {
        b.hasOwnProperty(e) || (b[e] = n.apply(null, q))
    }
    b = b ? b._modules : {};
    v(b, "Accessibility/Utils/HTMLUtilities.js", [b["Core/Globals.js"], b["Core/Utilities.js"]], function (b, e) {
        var w = b.doc,
            n = b.win,
            r = e.merge;
        return {
            addClass: function (b, m) {
                b.classList ? b.classList.add(m) : 0 > b.className.indexOf(m) && (b.className += m)
            },
            escapeStringForHTML: function (b) {
                return b.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#x27;").replace(/\//g, "&#x2F;")
            },
            getElement: function (b) {
                return w.getElementById(b)
            },
            getFakeMouseEvent: function (b) {
                if ("function" === typeof n.MouseEvent) return new n.MouseEvent(b);
                if (w.createEvent) {
                    var m = w.createEvent("MouseEvent");
                    if (m.initMouseEvent) return m.initMouseEvent(b,
                        !0, !0, n, "click" === b ? 1 : 0, 0, 0, 0, 0, !1, !1, !1, !1, 0, null), m
                }
                return {
                    type: b
                }
            },
            getHeadingTagNameForElement: function (b) {
                var m = function (b) {
                        b = parseInt(b.slice(1), 10);
                        return "h" + Math.min(6, b + 1)
                    },
                    l = function (b) {
                        var p;
                        a: {
                            for (p = b; p = p.previousSibling;) {
                                var g = p.tagName || "";
                                if (/H[1-6]/.test(g)) {
                                    p = g;
                                    break a
                                }
                            }
                            p = ""
                        }
                        if (p) return m(p);
                        b = b.parentElement;
                        if (!b) return "p";
                        p = b.tagName;
                        return /H[1-6]/.test(p) ? m(p) : l(b)
                    };
                return l(b)
            },
            removeElement: function (b) {
                b && b.parentNode && b.parentNode.removeChild(b)
            },
            reverseChildNodes: function (b) {
                for (var m =
                        b.childNodes.length; m--;) b.appendChild(b.childNodes[m])
            },
            setElAttrs: function (b, m) {
                Object.keys(m).forEach(function (l) {
                    var k = m[l];
                    null === k ? b.removeAttribute(l) : b.setAttribute(l, k)
                })
            },
            stripHTMLTagsFromString: function (b) {
                return "string" === typeof b ? b.replace(/<\/?[^>]+(>|$)/g, "") : b
            },
            visuallyHideElement: function (b) {
                r(!0, b.style, {
                    position: "absolute",
                    width: "1px",
                    height: "1px",
                    overflow: "hidden",
                    whiteSpace: "nowrap",
                    clip: "rect(1px, 1px, 1px, 1px)",
                    marginTop: "-3px",
                    "-ms-filter": "progid:DXImageTransform.Microsoft.Alpha(Opacity=1)",
                    filter: "alpha(opacity=1)",
                    opacity: "0.01"
                })
            }
        }
    });
    v(b, "Accessibility/Utils/ChartUtilities.js", [b["Accessibility/Utils/HTMLUtilities.js"], b["Core/Globals.js"], b["Core/Utilities.js"]], function (b, e, q) {
        function n(a) {
            var c = a.chart,
                d = {},
                b = "Seconds";
            d.Seconds = ((a.max || 0) - (a.min || 0)) / 1E3;
            d.Minutes = d.Seconds / 60;
            d.Hours = d.Minutes / 60;
            d.Days = d.Hours / 24;
            ["Minutes", "Hours", "Days"].forEach(function (a) {
                2 < d[a] && (b = a)
            });
            var g = d[b].toFixed("Seconds" !== b && "Minutes" !== b ? 1 : 0);
            return c.langFormat("accessibility.axis.timeRange" +
                b, {
                    chart: c,
                    axis: a,
                    range: g.replace(".0", "")
                })
        }

        function r(a) {
            var c = a.chart,
                d = c.options && c.options.accessibility && c.options.accessibility.screenReaderSection.axisRangeDateFormat || "",
                b = function (f) {
                    return a.dateTime ? c.time.dateFormat(d, a[f]) : a[f]
                };
            return c.langFormat("accessibility.axis.rangeFromTo", {
                chart: c,
                axis: a,
                rangeFrom: b("min"),
                rangeTo: b("max")
            })
        }

        function w(a) {
            if (a.points && a.points.length) return (a = c(a.points, function (a) {
                return !!a.graphic
            })) && a.graphic && a.graphic.element
        }

        function m(a) {
            var c = w(a);
            return c && c.parentNode || a.graph && a.graph.element || a.group && a.group.element
        }

        function l(a, c) {
            c.setAttribute("aria-hidden", !1);
            c !== a.renderTo && c.parentNode && c.parentNode !== p.body && (Array.prototype.forEach.call(c.parentNode.childNodes, function (a) {
                a.hasAttribute("aria-hidden") || a.setAttribute("aria-hidden", !0)
            }), l(a, c.parentNode))
        }
        var k = b.stripHTMLTagsFromString,
            p = e.doc,
            g = q.defined,
            c = q.find,
            a = q.fireEvent;
        return {
            getChartTitle: function (a) {
                return k(a.options.title.text || a.langFormat("accessibility.defaultChartTitle", {
                    chart: a
                }))
            },
            getAxisDescription: function (a) {
                return a && (a.userOptions && a.userOptions.accessibility && a.userOptions.accessibility.description || a.axisTitle && a.axisTitle.textStr || a.options.id || a.categories && "categories" || a.dateTime && "Time" || "values")
            },
            getAxisRangeDescription: function (a) {
                var c = a.options || {};
                return c.accessibility && "undefined" !== typeof c.accessibility.rangeDescription ? c.accessibility.rangeDescription : a.categories ? (c = a.chart, a = a.dataMax && a.dataMin ? c.langFormat("accessibility.axis.rangeCategories", {
                    chart: c,
                    axis: a,
                    numCategories: a.dataMax - a.dataMin + 1
                }) : "", a) : !a.dateTime || 0 !== a.min && 0 !== a.dataMin ? r(a) : n(a)
            },
            getPointFromXY: function (a, f, b) {
                for (var d = a.length, y; d--;)
                    if (y = c(a[d].points || [], function (a) {
                            return a.x === f && a.y === b
                        })) return y
            },
            getSeriesFirstPointElement: w,
            getSeriesFromName: function (a, c) {
                return c ? (a.series || []).filter(function (a) {
                    return a.name === c
                }) : a.series
            },
            getSeriesA11yElement: m,
            unhideChartElementFromAT: l,
            hideSeriesFromAT: function (a) {
                (a = m(a)) && a.setAttribute("aria-hidden", !0)
            },
            scrollToPoint: function (c) {
                var f =
                    c.series.xAxis,
                    b = c.series.yAxis,
                    d = f && f.scrollbar ? f : b;
                if ((f = d && d.scrollbar) && g(f.to) && g(f.from)) {
                    b = f.to - f.from;
                    if (g(d.dataMin) && g(d.dataMax)) {
                        var l = d.toPixels(d.dataMin),
                            p = d.toPixels(d.dataMax);
                        c = (d.toPixels(c["xAxis" === d.coll ? "x" : "y"] || 0) - l) / (p - l)
                    } else c = 0;
                    f.updatePosition(c - b / 2, c + b / 2);
                    a(f, "changed", {
                        from: f.from,
                        to: f.to,
                        trigger: "scrollbar",
                        DOMEvent: null
                    })
                }
            }
        }
    });
    v(b, "Accessibility/KeyboardNavigationHandler.js", [b["Core/Utilities.js"]], function (b) {
        function e(b, e) {
            this.chart = b;
            this.keyCodeMap = e.keyCodeMap || [];
            this.validate = e.validate;
            this.init = e.init;
            this.terminate = e.terminate;
            this.response = {
                success: 1,
                prev: 2,
                next: 3,
                noHandler: 4,
                fail: 5
            }
        }
        var w = b.find;
        e.prototype = {
            run: function (b) {
                var e = b.which || b.keyCode,
                    n = this.response.noHandler,
                    m = w(this.keyCodeMap, function (b) {
                        return -1 < b[0].indexOf(e)
                    });
                m ? n = m[1].call(this, e, b) : 9 === e && (n = this.response[b.shiftKey ? "prev" : "next"]);
                return n
            }
        };
        return e
    });
    v(b, "Accessibility/Utils/DOMElementProvider.js", [b["Core/Globals.js"], b["Accessibility/Utils/HTMLUtilities.js"], b["Core/Utilities.js"]],
        function (b, e, q) {
            var n = b.doc,
                w = e.removeElement;
            b = q.extend;
            e = function () {
                this.elements = []
            };
            b(e.prototype, {
                createElement: function () {
                    var b = n.createElement.apply(n, arguments);
                    this.elements.push(b);
                    return b
                },
                destroyCreatedElements: function () {
                    this.elements.forEach(function (b) {
                        w(b)
                    });
                    this.elements = []
                }
            });
            return e
        });
    v(b, "Accessibility/Utils/EventProvider.js", [b["Core/Globals.js"], b["Core/Utilities.js"]], function (b, e) {
        var w = e.addEvent;
        e = e.extend;
        var n = function () {
            this.eventRemovers = []
        };
        e(n.prototype, {
            addEvent: function () {
                var e =
                    w.apply(b, arguments);
                this.eventRemovers.push(e);
                return e
            },
            removeAddedEvents: function () {
                this.eventRemovers.forEach(function (b) {
                    b()
                });
                this.eventRemovers = []
            }
        });
        return n
    });
    v(b, "Accessibility/AccessibilityComponent.js", [b["Accessibility/Utils/ChartUtilities.js"], b["Accessibility/Utils/DOMElementProvider.js"], b["Accessibility/Utils/EventProvider.js"], b["Core/Globals.js"], b["Accessibility/Utils/HTMLUtilities.js"], b["Core/Utilities.js"]], function (b, e, q, n, r, x) {
        function m() {}
        var l = b.unhideChartElementFromAT,
            k = n.doc,
            p = n.win,
            g = r.removeElement,
            c = r.getFakeMouseEvent;
        b = x.extend;
        var a = x.fireEvent,
            d = x.merge;
        m.prototype = {
            initBase: function (a) {
                this.chart = a;
                this.eventProvider = new q;
                this.domElementProvider = new e;
                this.keyCodes = {
                    left: 37,
                    right: 39,
                    up: 38,
                    down: 40,
                    enter: 13,
                    space: 32,
                    esc: 27,
                    tab: 9
                }
            },
            addEvent: function () {
                return this.eventProvider.addEvent.apply(this.eventProvider, arguments)
            },
            createElement: function () {
                return this.domElementProvider.createElement.apply(this.domElementProvider, arguments)
            },
            fireEventOnWrappedOrUnwrappedElement: function (c,
                b) {
                var f = b.type;
                k.createEvent && (c.dispatchEvent || c.fireEvent) ? c.dispatchEvent ? c.dispatchEvent(b) : c.fireEvent(f, b) : a(c, f, b)
            },
            fakeClickEvent: function (a) {
                if (a) {
                    var b = c("click");
                    this.fireEventOnWrappedOrUnwrappedElement(a, b)
                }
            },
            addProxyGroup: function (a) {
                this.createOrUpdateProxyContainer();
                var c = this.createElement("div");
                Object.keys(a || {}).forEach(function (b) {
                    null !== a[b] && c.setAttribute(b, a[b])
                });
                this.chart.a11yProxyContainer.appendChild(c);
                return c
            },
            createOrUpdateProxyContainer: function () {
                var a = this.chart,
                    c = a.renderer.box;
                a.a11yProxyContainer = a.a11yProxyContainer || this.createProxyContainerElement();
                c.nextSibling !== a.a11yProxyContainer && a.container.insertBefore(a.a11yProxyContainer, c.nextSibling)
            },
            createProxyContainerElement: function () {
                var a = k.createElement("div");
                a.className = "highcharts-a11y-proxy-container";
                return a
            },
            createProxyButton: function (a, c, b, g, p) {
                var f = a.element,
                    y = this.createElement("button"),
                    k = d({
                        "aria-label": f.getAttribute("aria-label")
                    }, b);
                Object.keys(k).forEach(function (a) {
                    null !== k[a] &&
                        y.setAttribute(a, k[a])
                });
                y.className = "highcharts-a11y-proxy-button";
                a.hasClass("highcharts-no-tooltip") && (y.className += " highcharts-no-tooltip");
                p && this.addEvent(y, "click", p);
                this.setProxyButtonStyle(y);
                this.updateProxyButtonPosition(y, g || a);
                this.proxyMouseEventsForButton(f, y);
                c.appendChild(y);
                k["aria-hidden"] || l(this.chart, y);
                return y
            },
            getElementPosition: function (a) {
                var c = a.element;
                return (a = this.chart.renderTo) && c && c.getBoundingClientRect ? (c = c.getBoundingClientRect(), a = a.getBoundingClientRect(), {
                    x: c.left - a.left,
                    y: c.top - a.top,
                    width: c.right - c.left,
                    height: c.bottom - c.top
                }) : {
                    x: 0,
                    y: 0,
                    width: 1,
                    height: 1
                }
            },
            setProxyButtonStyle: function (a) {
                d(!0, a.style, {
                    borderWidth: "0",
                    backgroundColor: "transparent",
                    cursor: "pointer",
                    outline: "none",
                    opacity: "0.001",
                    filter: "alpha(opacity=1)",
                    zIndex: "999",
                    overflow: "hidden",
                    padding: "0",
                    margin: "0",
                    display: "block",
                    position: "absolute"
                });
                a.style["-ms-filter"] = "progid:DXImageTransform.Microsoft.Alpha(Opacity=1)"
            },
            updateProxyButtonPosition: function (a, c) {
                c = this.getElementPosition(c);
                d(!0, a.style, {
                    width: (c.width || 1) + "px",
                    height: (c.height || 1) + "px",
                    left: (Math.round(c.x) || 0) + "px",
                    top: (Math.round(c.y) || 0) + "px"
                })
            },
            proxyMouseEventsForButton: function (a, c) {
                var b = this;
                "click touchstart touchend touchcancel touchmove mouseover mouseenter mouseleave mouseout".split(" ").forEach(function (d) {
                    var f = 0 === d.indexOf("touch");
                    b.addEvent(c, d, function (c) {
                        var g = f ? b.cloneTouchEvent(c) : b.cloneMouseEvent(c);
                        a && b.fireEventOnWrappedOrUnwrappedElement(a, g);
                        c.stopPropagation();
                        "touchstart" !== d && "touchmove" !==
                            d && "touchend" !== d && c.preventDefault()
                    }, {
                        passive: !1
                    })
                })
            },
            cloneMouseEvent: function (a) {
                if ("function" === typeof p.MouseEvent) return new p.MouseEvent(a.type, a);
                if (k.createEvent) {
                    var b = k.createEvent("MouseEvent");
                    if (b.initMouseEvent) return b.initMouseEvent(a.type, a.bubbles, a.cancelable, a.view || p, a.detail, a.screenX, a.screenY, a.clientX, a.clientY, a.ctrlKey, a.altKey, a.shiftKey, a.metaKey, a.button, a.relatedTarget), b
                }
                return c(a.type)
            },
            cloneTouchEvent: function (a) {
                var c = function (a) {
                    for (var c = [], b = 0; b < a.length; ++b) {
                        var d =
                            a.item(b);
                        d && c.push(d)
                    }
                    return c
                };
                if ("function" === typeof p.TouchEvent) return c = new p.TouchEvent(a.type, {
                    touches: c(a.touches),
                    targetTouches: c(a.targetTouches),
                    changedTouches: c(a.changedTouches),
                    ctrlKey: a.ctrlKey,
                    shiftKey: a.shiftKey,
                    altKey: a.altKey,
                    metaKey: a.metaKey,
                    bubbles: a.bubbles,
                    cancelable: a.cancelable,
                    composed: a.composed,
                    detail: a.detail,
                    view: a.view
                }), a.defaultPrevented && c.preventDefault(), c;
                c = this.cloneMouseEvent(a);
                c.touches = a.touches;
                c.changedTouches = a.changedTouches;
                c.targetTouches = a.targetTouches;
                return c
            },
            destroyBase: function () {
                g(this.chart.a11yProxyContainer);
                this.domElementProvider.destroyCreatedElements();
                this.eventProvider.removeAddedEvents()
            }
        };
        b(m.prototype, {
            init: function () {},
            getKeyboardNavigation: function () {},
            onChartUpdate: function () {},
            onChartRender: function () {},
            destroy: function () {}
        });
        return m
    });
    v(b, "Accessibility/KeyboardNavigation.js", [b["Core/Chart/Chart.js"], b["Core/Globals.js"], b["Core/Utilities.js"], b["Accessibility/Utils/HTMLUtilities.js"], b["Accessibility/Utils/EventProvider.js"]],
        function (b, e, q, n, r) {
            function w(c, a) {
                this.init(c, a)
            }
            var m = e.doc,
                l = e.win,
                k = q.addEvent,
                p = q.fireEvent,
                g = n.getElement;
            k(m, "keydown", function (c) {
                27 === (c.which || c.keyCode) && e.charts && e.charts.forEach(function (a) {
                    a && a.dismissPopupContent && a.dismissPopupContent()
                })
            });
            b.prototype.dismissPopupContent = function () {
                var c = this;
                p(this, "dismissPopupContent", {}, function () {
                    c.tooltip && c.tooltip.hide(0);
                    c.hideExportMenu()
                })
            };
            w.prototype = {
                init: function (c, a) {
                    var b = this,
                        f = this.eventProvider = new r;
                    this.chart = c;
                    this.components =
                        a;
                    this.modules = [];
                    this.currentModuleIx = 0;
                    this.update();
                    f.addEvent(this.tabindexContainer, "keydown", function (a) {
                        return b.onKeydown(a)
                    });
                    f.addEvent(this.tabindexContainer, "focus", function (a) {
                        return b.onFocus(a)
                    });
                    ["mouseup", "touchend"].forEach(function (a) {
                        return f.addEvent(m, a, function () {
                            return b.onMouseUp()
                        })
                    });
                    ["mousedown", "touchstart"].forEach(function (a) {
                        return f.addEvent(c.renderTo, a, function () {
                            b.isClickingChart = !0
                        })
                    });
                    f.addEvent(c.renderTo, "mouseover", function () {
                        b.pointerIsOverChart = !0
                    });
                    f.addEvent(c.renderTo,
                        "mouseout",
                        function () {
                            b.pointerIsOverChart = !1
                        });
                    this.modules.length && this.modules[0].init(1)
                },
                update: function (c) {
                    var a = this.chart.options.accessibility;
                    a = a && a.keyboardNavigation;
                    var b = this.components;
                    this.updateContainerTabindex();
                    a && a.enabled && c && c.length ? (this.modules = c.reduce(function (a, c) {
                        c = b[c].getKeyboardNavigation();
                        return a.concat(c)
                    }, []), this.updateExitAnchor()) : (this.modules = [], this.currentModuleIx = 0, this.removeExitAnchor())
                },
                onFocus: function (c) {
                    var a = this.chart;
                    c = c.relatedTarget && a.container.contains(c.relatedTarget);
                    this.exiting || this.tabbingInBackwards || this.isClickingChart || c || !this.modules[0] || this.modules[0].init(1);
                    this.exiting = !1
                },
                onMouseUp: function () {
                    delete this.isClickingChart;
                    if (!this.keyboardReset && !this.pointerIsOverChart) {
                        var c = this.chart,
                            a = this.modules && this.modules[this.currentModuleIx || 0];
                        a && a.terminate && a.terminate();
                        c.focusElement && c.focusElement.removeFocusBorder();
                        this.currentModuleIx = 0;
                        this.keyboardReset = !0
                    }
                },
                onKeydown: function (c) {
                    c = c || l.event;
                    var a, b = this.modules && this.modules.length && this.modules[this.currentModuleIx];
                    this.exiting = this.keyboardReset = !1;
                    if (b) {
                        var f = b.run(c);
                        f === b.response.success ? a = !0 : f === b.response.prev ? a = this.prev() : f === b.response.next && (a = this.next());
                        a && (c.preventDefault(), c.stopPropagation())
                    }
                },
                prev: function () {
                    return this.move(-1)
                },
                next: function () {
                    return this.move(1)
                },
                move: function (c) {
                    var a = this.modules && this.modules[this.currentModuleIx];
                    a && a.terminate && a.terminate(c);
                    this.chart.focusElement && this.chart.focusElement.removeFocusBorder();
                    this.currentModuleIx += c;
                    if (a = this.modules && this.modules[this.currentModuleIx]) {
                        if (a.validate &&
                            !a.validate()) return this.move(c);
                        if (a.init) return a.init(c), !0
                    }
                    this.currentModuleIx = 0;
                    this.exiting = !0;
                    0 < c ? this.exitAnchor.focus() : this.tabindexContainer.focus();
                    return !1
                },
                updateExitAnchor: function () {
                    var c = g("highcharts-end-of-chart-marker-" + this.chart.index);
                    this.removeExitAnchor();
                    c ? (this.makeElementAnExitAnchor(c), this.exitAnchor = c) : this.createExitAnchor()
                },
                updateContainerTabindex: function () {
                    var c = this.chart.options.accessibility;
                    c = c && c.keyboardNavigation;
                    c = !(c && !1 === c.enabled);
                    var a = this.chart,
                        b = a.container;
                    a.renderTo.hasAttribute("tabindex") && (b.removeAttribute("tabindex"), b = a.renderTo);
                    this.tabindexContainer = b;
                    var f = b.getAttribute("tabindex");
                    c && !f ? b.setAttribute("tabindex", "0") : c || a.container.removeAttribute("tabindex")
                },
                makeElementAnExitAnchor: function (c) {
                    var a = this.tabindexContainer.getAttribute("tabindex") || 0;
                    c.setAttribute("class", "highcharts-exit-anchor");
                    c.setAttribute("tabindex", a);
                    c.setAttribute("aria-hidden", !1);
                    this.addExitAnchorEventsToEl(c)
                },
                createExitAnchor: function () {
                    var c =
                        this.chart,
                        a = this.exitAnchor = m.createElement("div");
                    c.renderTo.appendChild(a);
                    this.makeElementAnExitAnchor(a)
                },
                removeExitAnchor: function () {
                    this.exitAnchor && this.exitAnchor.parentNode && (this.exitAnchor.parentNode.removeChild(this.exitAnchor), delete this.exitAnchor)
                },
                addExitAnchorEventsToEl: function (c) {
                    var a = this.chart,
                        b = this;
                    this.eventProvider.addEvent(c, "focus", function (c) {
                        c = c || l.event;
                        c.relatedTarget && a.container.contains(c.relatedTarget) || b.exiting ? b.exiting = !1 : (b.tabbingInBackwards = !0, b.tabindexContainer.focus(),
                            delete b.tabbingInBackwards, c.preventDefault(), b.modules && b.modules.length && (b.currentModuleIx = b.modules.length - 1, (c = b.modules[b.currentModuleIx]) && c.validate && !c.validate() ? b.prev() : c && c.init(-1)))
                    })
                },
                destroy: function () {
                    this.removeExitAnchor();
                    this.eventProvider.removeAddedEvents();
                    this.chart.container.removeAttribute("tabindex")
                }
            };
            return w
        });
    v(b, "Accessibility/Components/LegendComponent.js", [b["Core/Animation/AnimationUtilities.js"], b["Core/Chart/Chart.js"], b["Core/Globals.js"], b["Core/Legend/Legend.js"],
        b["Core/Utilities.js"], b["Accessibility/AccessibilityComponent.js"], b["Accessibility/KeyboardNavigationHandler.js"], b["Accessibility/Utils/HTMLUtilities.js"], b["Accessibility/Utils/ChartUtilities.js"]
    ], function (b, e, q, n, r, x, m, l, k) {
        function p(a) {
            var c = a.legend && a.legend.allItems,
                b = a.options.legend.accessibility || {};
            return !(!c || !c.length || a.colorAxis && a.colorAxis.length || !1 === b.enabled)
        }
        var g = b.animObject,
            c = r.addEvent;
        b = r.extend;
        var a = r.find,
            d = r.fireEvent,
            f = r.isNumber,
            y = r.pick,
            G = r.syncTimeout,
            w = l.removeElement,
            L = l.stripHTMLTagsFromString,
            v = k.getChartTitle;
        e.prototype.highlightLegendItem = function (a) {
            var c = this.legend.allItems,
                b = this.accessibility && this.accessibility.components.legend.highlightedLegendItemIx;
            if (c[a]) {
                f(b) && c[b] && d(c[b].legendGroup.element, "mouseout");
                b = this.legend;
                var g = b.allItems[a].pageIx,
                    t = b.currentPage;
                "undefined" !== typeof g && g + 1 !== t && b.scroll(1 + g - t);
                this.setFocusToElement(c[a].legendItem, c[a].a11yProxyElement);
                d(c[a].legendGroup.element, "mouseover");
                return !0
            }
            return !1
        };
        c(n, "afterColorizeItem",
            function (a) {
                var c = a.item;
                this.chart.options.accessibility.enabled && c && c.a11yProxyElement && c.a11yProxyElement.setAttribute("aria-pressed", a.visible ? "true" : "false")
            });
        e = function () {};
        e.prototype = new x;
        b(e.prototype, {
            init: function () {
                var a = this;
                this.proxyElementsList = [];
                this.recreateProxies();
                this.addEvent(n, "afterScroll", function () {
                    this.chart === a.chart && (a.updateProxiesPositions(), a.updateLegendItemProxyVisibility(), this.chart.highlightLegendItem(a.highlightedLegendItemIx))
                });
                this.addEvent(n, "afterPositionItem",
                    function (c) {
                        this.chart === a.chart && this.chart.renderer && a.updateProxyPositionForItem(c.item)
                    });
                this.addEvent(n, "afterRender", function () {
                    this.chart === a.chart && this.chart.renderer && a.recreateProxies() && G(function () {
                        return a.updateProxiesPositions()
                    }, g(y(this.chart.renderer.globalAnimation, !0)).duration)
                })
            },
            updateLegendItemProxyVisibility: function () {
                var a = this.chart.legend,
                    c = a.currentPage || 1,
                    b = a.clipHeight || 0;
                (a.allItems || []).forEach(function (d) {
                    var t = d.pageIx || 0,
                        f = d._legendItemPos ? d._legendItemPos[1] :
                        0,
                        g = d.legendItem ? Math.round(d.legendItem.getBBox().height) : 0;
                    t = f + g - a.pages[t] > b || t !== c - 1;
                    d.a11yProxyElement && (d.a11yProxyElement.style.visibility = t ? "hidden" : "visible")
                })
            },
            onChartRender: function () {
                p(this.chart) || this.removeProxies()
            },
            onChartUpdate: function () {
                this.updateLegendTitle()
            },
            updateProxiesPositions: function () {
                for (var a = 0, c = this.proxyElementsList; a < c.length; a++) {
                    var b = c[a];
                    this.updateProxyButtonPosition(b.element, b.posElement)
                }
            },
            updateProxyPositionForItem: function (c) {
                var b = a(this.proxyElementsList,
                    function (a) {
                        return a.item === c
                    });
                b && this.updateProxyButtonPosition(b.element, b.posElement)
            },
            recreateProxies: function () {
                this.removeProxies();
                return p(this.chart) ? (this.addLegendProxyGroup(), this.addLegendListContainer(), this.proxyLegendItems(), this.updateLegendItemProxyVisibility(), !0) : !1
            },
            removeProxies: function () {
                w(this.legendProxyGroup);
                this.proxyElementsList = []
            },
            updateLegendTitle: function () {
                var a = this.chart,
                    c = L((a.legend && a.legend.options.title && a.legend.options.title.text || "").replace(/<br ?\/?>/g,
                        " "));
                a = a.langFormat("accessibility.legend.legendLabel" + (c ? "" : "NoTitle"), {
                    chart: a,
                    legendTitle: c,
                    chartTitle: v(a)
                });
                this.legendProxyGroup && this.legendProxyGroup.setAttribute("aria-label", a)
            },
            addLegendProxyGroup: function () {
                this.legendProxyGroup = this.addProxyGroup({
                    "aria-label": "_placeholder_",
                    role: "all" === this.chart.options.accessibility.landmarkVerbosity ? "region" : null
                })
            },
            addLegendListContainer: function () {
                if (this.legendProxyGroup) {
                    var a = this.legendListContainer = this.createElement("ul");
                    a.style.listStyle =
                        "none";
                    this.legendProxyGroup.appendChild(a)
                }
            },
            proxyLegendItems: function () {
                var a = this;
                (this.chart.legend && this.chart.legend.allItems || []).forEach(function (c) {
                    c.legendItem && c.legendItem.element && a.proxyLegendItem(c)
                })
            },
            proxyLegendItem: function (a) {
                if (a.legendItem && a.legendGroup && this.legendListContainer) {
                    var c = this.chart.langFormat("accessibility.legend.legendItem", {
                        chart: this.chart,
                        itemName: L(a.name),
                        item: a
                    });
                    c = {
                        tabindex: -1,
                        "aria-pressed": a.visible,
                        "aria-label": c
                    };
                    var b = a.legendGroup.div ? a.legendItem :
                        a.legendGroup,
                        d = this.createElement("li");
                    this.legendListContainer.appendChild(d);
                    a.a11yProxyElement = this.createProxyButton(a.legendItem, d, c, b);
                    this.proxyElementsList.push({
                        item: a,
                        element: a.a11yProxyElement,
                        posElement: b
                    })
                }
            },
            getKeyboardNavigation: function () {
                var a = this.keyCodes,
                    c = this,
                    b = this.chart;
                return new m(b, {
                    keyCodeMap: [
                        [
                            [a.left, a.right, a.up, a.down],
                            function (a) {
                                return c.onKbdArrowKey(this, a)
                            }
                        ],
                        [
                            [a.enter, a.space],
                            function (b) {
                                return q.isFirefox && b === a.space ? this.response.success : c.onKbdClick(this)
                            }
                        ]
                    ],
                    validate: function () {
                        return c.shouldHaveLegendNavigation()
                    },
                    init: function (a) {
                        return c.onKbdNavigationInit(a)
                    },
                    terminate: function () {
                        b.legend.allItems.forEach(function (a) {
                            return a.setState("", !0)
                        })
                    }
                })
            },
            onKbdArrowKey: function (a, c) {
                var b = this.keyCodes,
                    d = a.response,
                    t = this.chart,
                    f = t.options.accessibility,
                    g = t.legend.allItems.length;
                c = c === b.left || c === b.up ? -1 : 1;
                return t.highlightLegendItem(this.highlightedLegendItemIx + c) ? (this.highlightedLegendItemIx += c, d.success) : 1 < g && f.keyboardNavigation.wrapAround ? (a.init(c),
                    d.success) : d[0 < c ? "next" : "prev"]
            },
            onKbdClick: function (a) {
                var c = this.chart.legend.allItems[this.highlightedLegendItemIx];
                c && c.a11yProxyElement && d(c.a11yProxyElement, "click");
                return a.response.success
            },
            shouldHaveLegendNavigation: function () {
                var a = this.chart,
                    c = a.colorAxis && a.colorAxis.length,
                    b = (a.options.legend || {}).accessibility || {};
                return !!(a.legend && a.legend.allItems && a.legend.display && !c && b.enabled && b.keyboardNavigation && b.keyboardNavigation.enabled)
            },
            onKbdNavigationInit: function (a) {
                var c = this.chart,
                    b = c.legend.allItems.length - 1;
                a = 0 < a ? 0 : b;
                c.highlightLegendItem(a);
                this.highlightedLegendItemIx = a
            }
        });
        return e
    });
    v(b, "Accessibility/Components/MenuComponent.js", [b["Core/Chart/Chart.js"], b["Core/Utilities.js"], b["Accessibility/AccessibilityComponent.js"], b["Accessibility/KeyboardNavigationHandler.js"], b["Accessibility/Utils/ChartUtilities.js"], b["Accessibility/Utils/HTMLUtilities.js"]], function (b, e, q, n, r, x) {
        function m(c) {
            return c.exportSVGElements && c.exportSVGElements[0]
        }
        e = e.extend;
        var l = r.getChartTitle,
            k = r.unhideChartElementFromAT,
            p = x.removeElement,
            g = x.getFakeMouseEvent;
        b.prototype.showExportMenu = function () {
            var c = m(this);
            if (c && (c = c.element, c.onclick)) c.onclick(g("click"))
        };
        b.prototype.hideExportMenu = function () {
            var c = this.exportDivElements;
            c && this.exportContextMenu && (c.forEach(function (a) {
                if (a && "highcharts-menu-item" === a.className && a.onmouseout) a.onmouseout(g("mouseout"))
            }), this.highlightedExportItemIx = 0, this.exportContextMenu.hideMenu(), this.container.focus())
        };
        b.prototype.highlightExportItem = function (c) {
            var a =
                this.exportDivElements && this.exportDivElements[c],
                b = this.exportDivElements && this.exportDivElements[this.highlightedExportItemIx];
            if (a && "LI" === a.tagName && (!a.children || !a.children.length)) {
                var f = !!(this.renderTo.getElementsByTagName("g")[0] || {}).focus;
                a.focus && f && a.focus();
                if (b && b.onmouseout) b.onmouseout(g("mouseout"));
                if (a.onmouseover) a.onmouseover(g("mouseover"));
                this.highlightedExportItemIx = c;
                return !0
            }
            return !1
        };
        b.prototype.highlightLastExportItem = function () {
            var c;
            if (this.exportDivElements)
                for (c =
                    this.exportDivElements.length; c--;)
                    if (this.highlightExportItem(c)) return !0;
            return !1
        };
        b = function () {};
        b.prototype = new q;
        e(b.prototype, {
            init: function () {
                var c = this.chart,
                    a = this;
                this.addEvent(c, "exportMenuShown", function () {
                    a.onMenuShown()
                });
                this.addEvent(c, "exportMenuHidden", function () {
                    a.onMenuHidden()
                })
            },
            onMenuHidden: function () {
                var c = this.chart.exportContextMenu;
                c && c.setAttribute("aria-hidden", "true");
                this.isExportMenuShown = !1;
                this.setExportButtonExpandedState("false")
            },
            onMenuShown: function () {
                var c = this.chart,
                    a = c.exportContextMenu;
                a && (this.addAccessibleContextMenuAttribs(), k(c, a));
                this.isExportMenuShown = !0;
                this.setExportButtonExpandedState("true")
            },
            setExportButtonExpandedState: function (c) {
                var a = this.exportButtonProxy;
                a && a.setAttribute("aria-expanded", c)
            },
            onChartRender: function () {
                var c = this.chart,
                    a = c.options.accessibility;
                p(this.exportProxyGroup);
                var b = c.options.exporting,
                    f = m(c);
                b && !1 !== b.enabled && b.accessibility && b.accessibility.enabled && f && f.element && (this.exportProxyGroup = this.addProxyGroup("all" ===
                    a.landmarkVerbosity ? {
                        "aria-label": c.langFormat("accessibility.exporting.exportRegionLabel", {
                            chart: c,
                            chartTitle: l(c)
                        }),
                        role: "region"
                    } : {}), a = m(this.chart), this.exportButtonProxy = this.createProxyButton(a, this.exportProxyGroup, {
                    "aria-label": c.langFormat("accessibility.exporting.menuButtonLabel", {
                        chart: c
                    }),
                    "aria-expanded": !1
                }))
            },
            addAccessibleContextMenuAttribs: function () {
                var c = this.chart,
                    a = c.exportDivElements;
                a && a.length && (a.forEach(function (a) {
                    a && ("LI" !== a.tagName || a.children && a.children.length ? a.setAttribute("aria-hidden",
                        "true") : a.setAttribute("tabindex", -1))
                }), a = a[0] && a[0].parentNode) && (a.removeAttribute("aria-hidden"), a.setAttribute("aria-label", c.langFormat("accessibility.exporting.chartMenuLabel", {
                    chart: c
                })))
            },
            getKeyboardNavigation: function () {
                var c = this.keyCodes,
                    a = this.chart,
                    b = this;
                return new n(a, {
                    keyCodeMap: [
                        [
                            [c.left, c.up],
                            function () {
                                return b.onKbdPrevious(this)
                            }
                        ],
                        [
                            [c.right, c.down],
                            function () {
                                return b.onKbdNext(this)
                            }
                        ],
                        [
                            [c.enter, c.space],
                            function () {
                                return b.onKbdClick(this)
                            }
                        ]
                    ],
                    validate: function () {
                        return !!a.exporting &&
                            !1 !== a.options.exporting.enabled && !1 !== a.options.exporting.accessibility.enabled
                    },
                    init: function () {
                        var c = b.exportButtonProxy,
                            d = a.exportingGroup;
                        d && c && a.setFocusToElement(d, c)
                    },
                    terminate: function () {
                        a.hideExportMenu()
                    }
                })
            },
            onKbdPrevious: function (c) {
                var a = this.chart,
                    b = a.options.accessibility;
                c = c.response;
                for (var f = a.highlightedExportItemIx || 0; f--;)
                    if (a.highlightExportItem(f)) return c.success;
                return b.keyboardNavigation.wrapAround ? (a.highlightLastExportItem(), c.success) : c.prev
            },
            onKbdNext: function (c) {
                var a =
                    this.chart,
                    b = a.options.accessibility;
                c = c.response;
                for (var f = (a.highlightedExportItemIx || 0) + 1; f < a.exportDivElements.length; ++f)
                    if (a.highlightExportItem(f)) return c.success;
                return b.keyboardNavigation.wrapAround ? (a.highlightExportItem(0), c.success) : c.next
            },
            onKbdClick: function (c) {
                var a = this.chart,
                    b = a.exportDivElements[a.highlightedExportItemIx],
                    f = m(a).element;
                this.isExportMenuShown ? this.fakeClickEvent(b) : (this.fakeClickEvent(f), a.highlightExportItem(0));
                return c.response.success
            }
        });
        return b
    });
    v(b, "Accessibility/Components/SeriesComponent/SeriesKeyboardNavigation.js",
        [b["Core/Chart/Chart.js"], b["Core/Series/Point.js"], b["Core/Series/Series.js"], b["Core/Series/SeriesRegistry.js"], b["Core/Globals.js"], b["Core/Utilities.js"], b["Accessibility/KeyboardNavigationHandler.js"], b["Accessibility/Utils/EventProvider.js"], b["Accessibility/Utils/ChartUtilities.js"]],
        function (b, e, q, n, r, x, m, l, k) {
            function p(a) {
                var c = a.index,
                    b = a.series.points,
                    d = b.length;
                if (b[c] !== a)
                    for (; d--;) {
                        if (b[d] === a) return d
                    } else return c
            }

            function g(a) {
                var c = a.chart.options.accessibility.keyboardNavigation.seriesNavigation,
                    b = a.options.accessibility || {},
                    d = b.keyboardNavigation;
                return d && !1 === d.enabled || !1 === b.enabled || !1 === a.options.enableMouseTracking || !a.visible || c.pointNavigationEnabledThreshold && c.pointNavigationEnabledThreshold <= a.points.length
            }

            function c(a) {
                var c = a.series.chart.options.accessibility,
                    b = a.options.accessibility && !1 === a.options.accessibility.enabled;
                return a.isNull && c.keyboardNavigation.seriesNavigation.skipNullPoints || !1 === a.visible || !1 === a.isInside || b || g(a.series)
            }

            function a(a, c, b, d) {
                var f = Infinity,
                    t = c.points.length,
                    g = function (a) {
                        return !(C(a.plotX) && C(a.plotY))
                    };
                if (!g(a)) {
                    for (; t--;) {
                        var h = c.points[t];
                        if (!g(h) && (h = (a.plotX - h.plotX) * (a.plotX - h.plotX) * (b || 1) + (a.plotY - h.plotY) * (a.plotY - h.plotY) * (d || 1), h < f)) {
                            f = h;
                            var A = t
                        }
                    }
                    return C(A) ? c.points[A] : void 0
                }
            }

            function d(a) {
                var c = !1;
                delete a.highlightedPoint;
                return c = a.series.reduce(function (a, c) {
                    return a || c.highlightFirstValidPoint()
                }, !1)
            }

            function f(a, c) {
                this.keyCodes = c;
                this.chart = a
            }
            var y = n.seriesTypes,
                G = r.doc,
                C = x.defined;
            n = x.extend;
            var w = x.fireEvent,
                v =
                k.getPointFromXY,
                I = k.getSeriesFromName,
                D = k.scrollToPoint;
            q.prototype.keyboardMoveVertical = !0;
            ["column", "pie"].forEach(function (a) {
                y[a] && (y[a].prototype.keyboardMoveVertical = !1)
            });
            e.prototype.highlight = function () {
                var a = this.series.chart;
                if (this.isNull) a.tooltip && a.tooltip.hide(0);
                else this.onMouseOver();
                D(this);
                this.graphic && a.setFocusToElement(this.graphic);
                a.highlightedPoint = this;
                return this
            };
            b.prototype.highlightAdjacentPoint = function (a) {
                var b = this.series,
                    d = this.highlightedPoint,
                    f = d && p(d) || 0,
                    l =
                    d && d.series.points,
                    k = this.series && this.series[this.series.length - 1];
                k = k && k.points && k.points[k.points.length - 1];
                if (!b[0] || !b[0].points) return !1;
                if (d) {
                    if (b = b[d.series.index + (a ? 1 : -1)], f = l[f + (a ? 1 : -1)], !f && b && (f = b.points[a ? 0 : b.points.length - 1]), !f) return !1
                } else f = a ? b[0].points[0] : k;
                return c(f) ? (b = f.series, g(b) ? this.highlightedPoint = a ? b.points[b.points.length - 1] : b.points[0] : this.highlightedPoint = f, this.highlightAdjacentPoint(a)) : f.highlight()
            };
            q.prototype.highlightFirstValidPoint = function () {
                var a = this.chart.highlightedPoint,
                    b = (a && a.series) === this ? p(a) : 0;
                a = this.points;
                var d = a.length;
                if (a && d) {
                    for (var f = b; f < d; ++f)
                        if (!c(a[f])) return a[f].highlight();
                    for (; 0 <= b; --b)
                        if (!c(a[b])) return a[b].highlight()
                }
                return !1
            };
            b.prototype.highlightAdjacentSeries = function (c) {
                var b = this.highlightedPoint,
                    d = this.series && this.series[this.series.length - 1],
                    f = d && d.points && d.points[d.points.length - 1];
                if (!this.highlightedPoint) return d = c ? this.series && this.series[0] : d, (f = c ? d && d.points && d.points[0] : f) ? f.highlight() : !1;
                d = this.series[b.series.index + (c ?
                    -1 : 1)];
                if (!d) return !1;
                f = a(b, d, 4);
                if (!f) return !1;
                if (g(d)) return f.highlight(), c = this.highlightAdjacentSeries(c), c ? c : (b.highlight(), !1);
                f.highlight();
                return f.series.highlightFirstValidPoint()
            };
            b.prototype.highlightAdjacentPointVertical = function (a) {
                var b = this.highlightedPoint,
                    d = Infinity,
                    f;
                if (!C(b.plotX) || !C(b.plotY)) return !1;
                this.series.forEach(function (t) {
                    g(t) || t.points.forEach(function (g) {
                        if (C(g.plotY) && C(g.plotX) && g !== b) {
                            var k = g.plotY - b.plotY,
                                h = Math.abs(g.plotX - b.plotX);
                            h = Math.abs(k) * Math.abs(k) +
                                h * h * 4;
                            t.yAxis && t.yAxis.reversed && (k *= -1);
                            !(0 >= k && a || 0 <= k && !a || 5 > h || c(g)) && h < d && (d = h, f = g)
                        }
                    })
                });
                return f ? f.highlight() : !1
            };
            n(f.prototype, {
                init: function () {
                    var a = this,
                        c = this.chart,
                        b = this.eventProvider = new l;
                    b.addEvent(q, "destroy", function () {
                        return a.onSeriesDestroy(this)
                    });
                    b.addEvent(c, "afterDrilldown", function () {
                        d(this);
                        this.focusElement && this.focusElement.removeFocusBorder()
                    });
                    b.addEvent(c, "drilldown", function (c) {
                        c = c.point;
                        var b = c.series;
                        a.lastDrilledDownPoint = {
                            x: c.x,
                            y: c.y,
                            seriesName: b ? b.name : ""
                        }
                    });
                    b.addEvent(c, "drillupall", function () {
                        setTimeout(function () {
                            a.onDrillupAll()
                        }, 10)
                    });
                    b.addEvent(e, "afterSetState", function () {
                        var a = this.graphic && this.graphic.element;
                        c.highlightedPoint === this && G.activeElement !== a && a && a.focus && a.focus()
                    })
                },
                onDrillupAll: function () {
                    var a = this.lastDrilledDownPoint,
                        c = this.chart,
                        b = a && I(c, a.seriesName),
                        d;
                    a && b && C(a.x) && C(a.y) && (d = v(b, a.x, a.y));
                    c.container && c.container.focus();
                    d && d.highlight && d.highlight();
                    c.focusElement && c.focusElement.removeFocusBorder()
                },
                getKeyboardNavigationHandler: function () {
                    var a =
                        this,
                        c = this.keyCodes,
                        b = this.chart,
                        d = b.inverted;
                    return new m(b, {
                        keyCodeMap: [
                            [d ? [c.up, c.down] : [c.left, c.right], function (c) {
                                return a.onKbdSideways(this, c)
                            }],
                            [d ? [c.left, c.right] : [c.up, c.down], function (c) {
                                return a.onKbdVertical(this, c)
                            }],
                            [
                                [c.enter, c.space],
                                function (a, c) {
                                    if (a = b.highlightedPoint) c.point = a, w(a.series, "click", c), a.firePointEvent("click");
                                    return this.response.success
                                }
                            ]
                        ],
                        init: function (c) {
                            return a.onHandlerInit(this, c)
                        },
                        terminate: function () {
                            return a.onHandlerTerminate()
                        }
                    })
                },
                onKbdSideways: function (a,
                    c) {
                    var b = this.keyCodes;
                    return this.attemptHighlightAdjacentPoint(a, c === b.right || c === b.down)
                },
                onKbdVertical: function (a, c) {
                    var b = this.chart,
                        d = this.keyCodes;
                    c = c === d.down || c === d.right;
                    d = b.options.accessibility.keyboardNavigation.seriesNavigation;
                    if (d.mode && "serialize" === d.mode) return this.attemptHighlightAdjacentPoint(a, c);
                    b[b.highlightedPoint && b.highlightedPoint.series.keyboardMoveVertical ? "highlightAdjacentPointVertical" : "highlightAdjacentSeries"](c);
                    return a.response.success
                },
                onHandlerInit: function (a,
                    c) {
                    var b = this.chart;
                    if (0 < c) d(b);
                    else {
                        c = b.series.length;
                        for (var f; c-- && !(b.highlightedPoint = b.series[c].points[b.series[c].points.length - 1], f = b.series[c].highlightFirstValidPoint()););
                    }
                    return a.response.success
                },
                onHandlerTerminate: function () {
                    var a = this.chart;
                    a.tooltip && a.tooltip.hide(0);
                    var c = a.highlightedPoint && a.highlightedPoint.series;
                    if (c && c.onMouseOut) c.onMouseOut();
                    if (a.highlightedPoint && a.highlightedPoint.onMouseOut) a.highlightedPoint.onMouseOut();
                    delete a.highlightedPoint
                },
                attemptHighlightAdjacentPoint: function (a,
                    c) {
                    var b = this.chart,
                        d = b.options.accessibility.keyboardNavigation.wrapAround;
                    return b.highlightAdjacentPoint(c) ? a.response.success : d ? a.init(c ? 1 : -1) : a.response[c ? "next" : "prev"]
                },
                onSeriesDestroy: function (a) {
                    var c = this.chart;
                    c.highlightedPoint && c.highlightedPoint.series === a && (delete c.highlightedPoint, c.focusElement && c.focusElement.removeFocusBorder())
                },
                destroy: function () {
                    this.eventProvider.removeAddedEvents()
                }
            });
            return f
        });
    v(b, "Accessibility/Components/AnnotationsA11y.js", [b["Accessibility/Utils/HTMLUtilities.js"]],
        function (b) {
            function e(b) {
                return (b.annotations || []).reduce(function (b, l) {
                    l.options && !1 !== l.options.visible && (b = b.concat(l.labels));
                    return b
                }, [])
            }

            function q(b) {
                return b.options && b.options.accessibility && b.options.accessibility.description || b.graphic && b.graphic.text && b.graphic.text.textStr || ""
            }

            function n(b) {
                var k = b.options && b.options.accessibility && b.options.accessibility.description;
                if (k) return k;
                k = b.chart;
                var p = q(b),
                    g = b.points.filter(function (a) {
                        return !!a.graphic
                    }).map(function (a) {
                        var c = a.accessibility &&
                            a.accessibility.valueDescription || a.graphic && a.graphic.element && a.graphic.element.getAttribute("aria-label") || "";
                        a = a && a.series.name || "";
                        return (a ? a + ", " : "") + "data point " + c
                    }).filter(function (a) {
                        return !!a
                    }),
                    c = g.length,
                    a = "accessibility.screenReaderSection.annotations.description" + (1 < c ? "MultiplePoints" : c ? "SinglePoint" : "NoPoints");
                b = {
                    annotationText: p,
                    annotation: b,
                    numPoints: c,
                    annotationPoint: g[0],
                    additionalAnnotationPoints: g.slice(1)
                };
                return k.langFormat(a, b)
            }

            function r(b) {
                return e(b).map(function (b) {
                    return (b =
                        x(m(n(b)))) ? "<li>" + b + "</li>" : ""
                })
            }
            var x = b.escapeStringForHTML,
                m = b.stripHTMLTagsFromString;
            return {
                getAnnotationsInfoHTML: function (b) {
                    var k = b.annotations;
                    return k && k.length ? '<ul style="list-style-type: none">' + r(b).join(" ") + "</ul>" : ""
                },
                getAnnotationLabelDescription: n,
                getAnnotationListItems: r,
                getPointAnnotationTexts: function (b) {
                    var k = e(b.series.chart).filter(function (k) {
                        return -1 < k.points.indexOf(b)
                    });
                    return k.length ? k.map(function (b) {
                        return "" + q(b)
                    }) : []
                }
            }
        });
    v(b, "Accessibility/Components/SeriesComponent/SeriesDescriber.js",
        [b["Accessibility/Components/AnnotationsA11y.js"], b["Accessibility/Utils/ChartUtilities.js"], b["Core/FormatUtilities.js"], b["Accessibility/Utils/HTMLUtilities.js"], b["Core/Utilities.js"]],
        function (b, e, q, n, r) {
            function x(a) {
                var c = a.index;
                return a.series && a.series.data && H(c) ? h(a.series.data, function (a) {
                    return !!(a && "undefined" !== typeof a.index && a.index > c && a.graphic && a.graphic.element)
                }) || null : null
            }

            function m(a) {
                var c = a.chart.options.accessibility.series.pointDescriptionEnabledThreshold;
                return !!(!1 !==
                    c && a.points && a.points.length >= c)
            }

            function l(a) {
                var c = a.options.accessibility || {};
                return !m(a) && !c.exposeAsGroupOnly
            }

            function k(a) {
                var c = a.chart.options.accessibility.keyboardNavigation.seriesNavigation;
                return !(!a.points || !(a.points.length < c.pointNavigationEnabledThreshold || !1 === c.pointNavigationEnabledThreshold))
            }

            function p(a, c) {
                var b = a.series.chart,
                    h = b.options.accessibility.point || {};
                a = a.series.tooltipOptions || {};
                b = b.options.lang;
                return A(c) ? B(c, h.valueDecimals || a.valueDecimals || -1, b.decimalPoint,
                    b.accessibility.thousandsSep || b.thousandsSep) : c
            }

            function g(a) {
                var c = (a.options.accessibility || {}).description;
                return c && a.chart.langFormat("accessibility.series.description", {
                    description: c,
                    series: a
                }) || ""
            }

            function c(a, c) {
                return a.chart.langFormat("accessibility.series." + c + "Description", {
                    name: D(a[c]),
                    series: a
                })
            }

            function a(a) {
                var c = a.series,
                    b = c.chart,
                    h = b.options.accessibility.point || {};
                if (c = c.xAxis && c.xAxis.dateTime) return c = c.getXDateFormat(a.x || 0, b.options.tooltip.dateTimeLabelFormats), h = h.dateFormatter &&
                    h.dateFormatter(a) || h.dateFormat || c, b.time.dateFormat(h, a.x || 0, void 0)
            }

            function d(c) {
                var b = a(c),
                    h = (c.series.xAxis || {}).categories && H(c.category) && ("" + c.category).replace("<br/>", " "),
                    d = c.id && 0 > c.id.indexOf("highcharts-"),
                    f = "x, " + c.x;
                return c.name || b || h || (d ? c.id : f)
            }

            function f(a, c, b) {
                var h = c || "",
                    d = b || "";
                return a.series.pointArrayMap.reduce(function (c, b) {
                    c += c.length ? ", " : "";
                    var f = p(a, J(a[b], a.options[b]));
                    return c + (b + ": " + h + f + d)
                }, "")
            }

            function y(a) {
                var c = a.series,
                    b = c.chart.options.accessibility.point || {},
                    h = c.tooltipOptions || {},
                    d = b.valuePrefix || h.valuePrefix || "";
                b = b.valueSuffix || h.valueSuffix || "";
                h = p(a, a["undefined" !== typeof a.value ? "value" : "y"]);
                return a.isNull ? c.chart.langFormat("accessibility.series.nullPointValue", {
                    point: a
                }) : c.pointArrayMap ? f(a, d, b) : d + h + b
            }

            function G(a) {
                var c = a.series,
                    b = c.chart,
                    h = b.options.accessibility.point.valueDescriptionFormat,
                    f = (c = J(c.xAxis && c.xAxis.options.accessibility && c.xAxis.options.accessibility.enabled, !b.angular)) ? d(a) : "";
                a = {
                    point: a,
                    index: H(a.index) ? a.index + 1 : "",
                    xDescription: f,
                    value: y(a),
                    separator: c ? ", " : ""
                };
                return u(h, a, b)
            }

            function C(a) {
                var c = a.series,
                    b = c.chart,
                    h = G(a),
                    d = a.options && a.options.accessibility && a.options.accessibility.description;
                d = d ? " " + d : "";
                c = 1 < b.series.length && c.name ? " " + c.name + "." : "";
                b = a.series.chart;
                var f = I(a),
                    g = {
                        point: a,
                        annotations: f
                    };
                b = f.length ? b.langFormat("accessibility.series.pointAnnotationsDescription", g) : "";
                a.accessibility = a.accessibility || {};
                a.accessibility.valueDescription = h;
                return h + d + c + (b ? " " + b : "")
            }

            function w(a) {
                var c = l(a),
                    b =
                    k(a);
                (c || b) && a.points.forEach(function (a) {
                    var b;
                    if (!(b = a.graphic && a.graphic.element) && (b = a.series && a.series.is("sunburst"), b = a.isNull && !b)) {
                        var h = a.series,
                            d = x(a);
                        h = (b = d && d.graphic) ? b.parentGroup : h.graph || h.group;
                        d = d ? {
                            x: J(a.plotX, d.plotX, 0),
                            y: J(a.plotY, d.plotY, 0)
                        } : {
                            x: J(a.plotX, 0),
                            y: J(a.plotY, 0)
                        };
                        d = a.series.chart.renderer.rect(d.x, d.y, 1, 1);
                        d.attr({
                            "class": "highcharts-a11y-dummy-point",
                            fill: "none",
                            opacity: 0,
                            "fill-opacity": 0,
                            "stroke-opacity": 0
                        });
                        h && h.element ? (a.graphic = d, a.hasDummyGraphic = !0, d.add(h),
                            h.element.insertBefore(d.element, b ? b.element : null), b = d.element) : b = void 0
                    }
                    h = a.options && a.options.accessibility && !1 === a.options.accessibility.enabled;
                    b && (b.setAttribute("tabindex", "-1"), b.style.outline = "0", c && !h ? (d = a.series, h = d.chart.options.accessibility.point || {}, d = d.options.accessibility || {}, a = F(d.pointDescriptionFormatter && d.pointDescriptionFormatter(a) || h.descriptionFormatter && h.descriptionFormatter(a) || C(a)), b.setAttribute("role", "img"), b.setAttribute("aria-label", a)) : b.setAttribute("aria-hidden",
                        !0))
                })
            }

            function v(a) {
                var b = a.chart,
                    h = b.types || [],
                    d = g(a),
                    f = function (c) {
                        return b[c] && 1 < b[c].length && a[c]
                    },
                    A = c(a, "xAxis"),
                    H = c(a, "yAxis"),
                    k = {
                        name: a.name || "",
                        ix: a.index + 1,
                        numSeries: b.series && b.series.length,
                        numPoints: a.points && a.points.length,
                        series: a
                    };
                h = 1 < h.length ? "Combination" : "";
                return (b.langFormat("accessibility.series.summary." + a.type + h, k) || b.langFormat("accessibility.series.summary.default" + h, k)) + (d ? " " + d : "") + (f("yAxis") ? " " + H : "") + (f("xAxis") ? " " + A : "")
            }
            var I = b.getPointAnnotationTexts,
                D = e.getAxisDescription,
                z = e.getSeriesFirstPointElement,
                E = e.getSeriesA11yElement,
                t = e.unhideChartElementFromAT,
                u = q.format,
                B = q.numberFormat,
                K = n.reverseChildNodes,
                F = n.stripHTMLTagsFromString,
                h = r.find,
                A = r.isNumber,
                J = r.pick,
                H = r.defined;
            return {
                describeSeries: function (a) {
                    var c = a.chart,
                        b = z(a),
                        h = E(a),
                        d = c.is3d && c.is3d();
                    if (h) {
                        h.lastChild !== b || d || K(h);
                        w(a);
                        t(c, h);
                        d = a.chart;
                        c = d.options.chart;
                        b = 1 < d.series.length;
                        d = d.options.accessibility.series.describeSingleSeries;
                        var f = (a.options.accessibility || {}).exposeAsGroupOnly;
                        c.options3d && c.options3d.enabled &&
                            b || !(b || d || f || m(a)) ? h.setAttribute("aria-label", "") : (c = a.chart.options.accessibility, b = c.landmarkVerbosity, (a.options.accessibility || {}).exposeAsGroupOnly ? h.setAttribute("role", "img") : "all" === b && h.setAttribute("role", "region"), h.setAttribute("tabindex", "-1"), h.style.outline = "0", h.setAttribute("aria-label", F(c.series.descriptionFormatter && c.series.descriptionFormatter(a) || v(a))))
                    }
                },
                defaultPointDescriptionFormatter: C,
                defaultSeriesDescriptionFormatter: v,
                getPointA11yTimeDescription: a,
                getPointXDescription: d,
                getPointValue: y,
                getPointValueDescription: G
            }
        });
    v(b, "Accessibility/Utils/Announcer.js", [b["Core/Globals.js"], b["Core/Renderer/HTML/AST.js"], b["Accessibility/Utils/DOMElementProvider.js"], b["Accessibility/Utils/HTMLUtilities.js"]], function (b, e, q, n) {
        var r = b.doc,
            x = n.setElAttrs,
            m = n.visuallyHideElement;
        return function () {
            function b(b, p) {
                this.chart = b;
                this.domElementProvider = new q;
                this.announceRegion = this.addAnnounceRegion(p)
            }
            b.prototype.destroy = function () {
                this.domElementProvider.destroyCreatedElements()
            };
            b.prototype.announce = function (b) {
                var k = this;
                e.setElementHTML(this.announceRegion, b);
                this.clearAnnouncementRegionTimer && clearTimeout(this.clearAnnouncementRegionTimer);
                this.clearAnnouncementRegionTimer = setTimeout(function () {
                    k.announceRegion.innerHTML = "";
                    delete k.clearAnnouncementRegionTimer
                }, 1E3)
            };
            b.prototype.addAnnounceRegion = function (b) {
                var k = this.chart.announcerContainer || this.createAnnouncerContainer(),
                    g = this.domElementProvider.createElement("div");
                x(g, {
                    "aria-hidden": !1,
                    "aria-live": b
                });
                m(g);
                k.appendChild(g);
                return g
            };
            b.prototype.createAnnouncerContainer = function () {
                var b = this.chart,
                    p = r.createElement("div");
                x(p, {
                    "aria-hidden": !1,
                    style: "position:relative",
                    "class": "highcharts-announcer-container"
                });
                b.renderTo.insertBefore(p, b.renderTo.firstChild);
                return b.announcerContainer = p
            };
            return b
        }()
    });
    v(b, "Accessibility/Components/SeriesComponent/NewDataAnnouncer.js", [b["Core/Globals.js"], b["Core/Series/Series.js"], b["Core/Utilities.js"], b["Accessibility/Utils/ChartUtilities.js"], b["Accessibility/Components/SeriesComponent/SeriesDescriber.js"],
        b["Accessibility/Utils/Announcer.js"], b["Accessibility/Utils/EventProvider.js"]
    ], function (b, e, q, n, r, x, m) {
        function l(a) {
            var c = a.series.data.filter(function (c) {
                return a.x === c.x && a.y === c.y
            });
            return 1 === c.length ? c[0] : a
        }

        function k(a, c) {
            var b = (a || []).concat(c || []).reduce(function (a, c) {
                a[c.name + c.index] = c;
                return a
            }, {});
            return Object.keys(b).map(function (a) {
                return b[a]
            })
        }
        var p = q.extend,
            g = q.defined,
            c = n.getChartTitle,
            a = r.defaultPointDescriptionFormatter,
            d = r.defaultSeriesDescriptionFormatter;
        q = function (a) {
            this.chart =
                a
        };
        p(q.prototype, {
            init: function () {
                var a = this.chart,
                    c = a.options.accessibility.announceNewData.interruptUser ? "assertive" : "polite";
                this.lastAnnouncementTime = 0;
                this.dirty = {
                    allSeries: {}
                };
                this.eventProvider = new m;
                this.announcer = new x(a, c);
                this.addEventListeners()
            },
            destroy: function () {
                this.eventProvider.removeAddedEvents();
                this.announcer.destroy()
            },
            addEventListeners: function () {
                var a = this,
                    c = this.chart,
                    b = this.eventProvider;
                b.addEvent(c, "afterDrilldown", function () {
                    a.lastAnnouncementTime = 0
                });
                b.addEvent(e, "updatedData",
                    function () {
                        a.onSeriesUpdatedData(this)
                    });
                b.addEvent(c, "afterAddSeries", function (c) {
                    a.onSeriesAdded(c.series)
                });
                b.addEvent(e, "addPoint", function (c) {
                    a.onPointAdded(c.point)
                });
                b.addEvent(c, "redraw", function () {
                    a.announceDirtyData()
                })
            },
            onSeriesUpdatedData: function (a) {
                var c = this.chart;
                a.chart === c && c.options.accessibility.announceNewData.enabled && (this.dirty.hasDirty = !0, this.dirty.allSeries[a.name + a.index] = a)
            },
            onSeriesAdded: function (a) {
                this.chart.options.accessibility.announceNewData.enabled && (this.dirty.hasDirty = !0, this.dirty.allSeries[a.name + a.index] = a, this.dirty.newSeries = g(this.dirty.newSeries) ? void 0 : a)
            },
            onPointAdded: function (a) {
                var c = a.series.chart;
                this.chart === c && c.options.accessibility.announceNewData.enabled && (this.dirty.newPoint = g(this.dirty.newPoint) ? void 0 : a)
            },
            announceDirtyData: function () {
                var a = this;
                if (this.chart.options.accessibility.announceNewData && this.dirty.hasDirty) {
                    var c = this.dirty.newPoint;
                    c && (c = l(c));
                    this.queueAnnouncement(Object.keys(this.dirty.allSeries).map(function (c) {
                            return a.dirty.allSeries[c]
                        }),
                        this.dirty.newSeries, c);
                    this.dirty = {
                        allSeries: {}
                    }
                }
            },
            queueAnnouncement: function (a, c, b) {
                var d = this,
                    f = this.chart.options.accessibility.announceNewData;
                if (f.enabled) {
                    var g = +new Date;
                    f = Math.max(0, f.minAnnounceInterval - (g - this.lastAnnouncementTime));
                    a = k(this.queuedAnnouncement && this.queuedAnnouncement.series, a);
                    if (c = this.buildAnnouncementMessage(a, c, b)) this.queuedAnnouncement && clearTimeout(this.queuedAnnouncementTimer), this.queuedAnnouncement = {
                        time: g,
                        message: c,
                        series: a
                    }, this.queuedAnnouncementTimer = setTimeout(function () {
                        d &&
                            d.announcer && (d.lastAnnouncementTime = +new Date, d.announcer.announce(d.queuedAnnouncement.message), delete d.queuedAnnouncement, delete d.queuedAnnouncementTimer)
                    }, f)
                }
            },
            buildAnnouncementMessage: function (f, g, k) {
                var p = this.chart,
                    l = p.options.accessibility.announceNewData;
                if (l.announcementFormatter && (f = l.announcementFormatter(f, g, k), !1 !== f)) return f.length ? f : null;
                f = b.charts && 1 < b.charts.length ? "Multiple" : "Single";
                f = g ? "newSeriesAnnounce" + f : k ? "newPointAnnounce" + f : "newDataAnnounce";
                l = c(p);
                return p.langFormat("accessibility.announceNewData." +
                    f, {
                        chartTitle: l,
                        seriesDesc: g ? d(g) : null,
                        pointDesc: k ? a(k) : null,
                        point: k,
                        series: g
                    })
            }
        });
        return q
    });
    v(b, "Accessibility/Components/SeriesComponent/ForcedMarkers.js", [b["Core/Series/Series.js"], b["Core/Utilities.js"]], function (b, e) {
        function q(b) {
            r(!0, b, {
                marker: {
                    enabled: !0,
                    states: {
                        normal: {
                            opacity: 0
                        }
                    }
                }
            })
        }
        var n = e.addEvent,
            r = e.merge;
        return function () {
            n(b, "render", function () {
                var b = this.options,
                    e = !1 !== (this.options.accessibility && this.options.accessibility.enabled);
                if (e = this.chart.options.accessibility.enabled &&
                    e) e = this.chart.options.accessibility, e = this.points.length < e.series.pointDescriptionEnabledThreshold || !1 === e.series.pointDescriptionEnabledThreshold;
                if (e) {
                    if (b.marker && !1 === b.marker.enabled && (this.a11yMarkersForced = !0, q(this.options)), this._hasPointMarkers && this.points && this.points.length)
                        for (b = this.points.length; b--;) {
                            e = this.points[b];
                            var l = e.options;
                            delete e.hasForcedA11yMarker;
                            l.marker && (l.marker.enabled ? (r(!0, l.marker, {
                                states: {
                                    normal: {
                                        opacity: l.marker.states && l.marker.states.normal && l.marker.states.normal.opacity ||
                                            1
                                    }
                                }
                            }), e.hasForcedA11yMarker = !1) : (q(l), e.hasForcedA11yMarker = !0))
                        }
                } else this.a11yMarkersForced && (delete this.a11yMarkersForced, (b = this.resetA11yMarkerOptions) && r(!0, this.options, {
                    marker: {
                        enabled: b.enabled,
                        states: {
                            normal: {
                                opacity: b.states && b.states.normal && b.states.normal.opacity
                            }
                        }
                    }
                }))
            });
            n(b, "afterSetOptions", function (b) {
                this.resetA11yMarkerOptions = r(b.options.marker || {}, this.userOptions.marker || {})
            });
            n(b, "afterRender", function () {
                if (this.chart.styledMode) {
                    if (this.markerGroup) this.markerGroup[this.a11yMarkersForced ?
                        "addClass" : "removeClass"]("highcharts-a11y-markers-hidden");
                    this._hasPointMarkers && this.points && this.points.length && this.points.forEach(function (b) {
                        b.graphic && (b.graphic[b.hasForcedA11yMarker ? "addClass" : "removeClass"]("highcharts-a11y-marker-hidden"), b.graphic[!1 === b.hasForcedA11yMarker ? "addClass" : "removeClass"]("highcharts-a11y-marker-visible"))
                    })
                }
            })
        }
    });
    v(b, "Accessibility/Components/SeriesComponent/SeriesComponent.js", [b["Core/Globals.js"], b["Core/Utilities.js"], b["Accessibility/AccessibilityComponent.js"],
        b["Accessibility/Components/SeriesComponent/SeriesKeyboardNavigation.js"], b["Accessibility/Components/SeriesComponent/NewDataAnnouncer.js"], b["Accessibility/Components/SeriesComponent/ForcedMarkers.js"], b["Accessibility/Utils/ChartUtilities.js"], b["Accessibility/Components/SeriesComponent/SeriesDescriber.js"], b["Core/Tooltip.js"]
    ], function (b, e, q, n, r, x, m, l, k) {
        e = e.extend;
        var p = m.hideSeriesFromAT,
            g = l.describeSeries;
        b.SeriesAccessibilityDescriber = l;
        x();
        b = function () {};
        b.prototype = new q;
        e(b.prototype, {
            init: function () {
                this.newDataAnnouncer =
                    new r(this.chart);
                this.newDataAnnouncer.init();
                this.keyboardNavigation = new n(this.chart, this.keyCodes);
                this.keyboardNavigation.init();
                this.hideTooltipFromATWhenShown();
                this.hideSeriesLabelsFromATWhenShown()
            },
            hideTooltipFromATWhenShown: function () {
                var c = this;
                this.addEvent(k, "refresh", function () {
                    this.chart === c.chart && this.label && this.label.element && this.label.element.setAttribute("aria-hidden", !0)
                })
            },
            hideSeriesLabelsFromATWhenShown: function () {
                this.addEvent(this.chart, "afterDrawSeriesLabels", function () {
                    this.series.forEach(function (c) {
                        c.labelBySeries &&
                            c.labelBySeries.attr("aria-hidden", !0)
                    })
                })
            },
            onChartRender: function () {
                this.chart.series.forEach(function (c) {
                    !1 !== (c.options.accessibility && c.options.accessibility.enabled) && c.visible ? g(c) : p(c)
                })
            },
            getKeyboardNavigation: function () {
                return this.keyboardNavigation.getKeyboardNavigationHandler()
            },
            destroy: function () {
                this.newDataAnnouncer.destroy();
                this.keyboardNavigation.destroy()
            }
        });
        return b
    });
    v(b, "Accessibility/Components/ZoomComponent.js", [b["Accessibility/AccessibilityComponent.js"], b["Accessibility/Utils/ChartUtilities.js"],
        b["Core/Globals.js"], b["Accessibility/Utils/HTMLUtilities.js"], b["Accessibility/KeyboardNavigationHandler.js"], b["Core/Utilities.js"]
    ], function (b, e, q, n, r, x) {
        var m = e.unhideChartElementFromAT;
        e = q.noop;
        var l = n.removeElement,
            k = n.setElAttrs;
        n = x.extend;
        var p = x.pick;
        q.Axis.prototype.panStep = function (b, c) {
            var a = c || 3;
            c = this.getExtremes();
            var d = (c.max - c.min) / a * b;
            a = c.max + d;
            d = c.min + d;
            var f = a - d;
            0 > b && d < c.dataMin ? (d = c.dataMin, a = d + f) : 0 < b && a > c.dataMax && (a = c.dataMax, d = a - f);
            this.setExtremes(d, a)
        };
        e.prototype = new b;
        n(e.prototype, {
            init: function () {
                var b = this,
                    c = this.chart;
                ["afterShowResetZoom", "afterDrilldown", "drillupall"].forEach(function (a) {
                    b.addEvent(c, a, function () {
                        b.updateProxyOverlays()
                    })
                })
            },
            onChartUpdate: function () {
                var b = this.chart,
                    c = this;
                b.mapNavButtons && b.mapNavButtons.forEach(function (a, d) {
                    m(b, a.element);
                    c.setMapNavButtonAttrs(a.element, "accessibility.zoom.mapZoom" + (d ? "Out" : "In"))
                })
            },
            setMapNavButtonAttrs: function (b, c) {
                var a = this.chart;
                c = a.langFormat(c, {
                    chart: a
                });
                k(b, {
                    tabindex: -1,
                    role: "button",
                    "aria-label": c
                })
            },
            onChartRender: function () {
                this.updateProxyOverlays()
            },
            updateProxyOverlays: function () {
                var b = this.chart;
                l(this.drillUpProxyGroup);
                l(this.resetZoomProxyGroup);
                b.resetZoomButton && this.recreateProxyButtonAndGroup(b.resetZoomButton, "resetZoomProxyButton", "resetZoomProxyGroup", b.langFormat("accessibility.zoom.resetZoomButton", {
                    chart: b
                }));
                b.drillUpButton && this.recreateProxyButtonAndGroup(b.drillUpButton, "drillUpProxyButton", "drillUpProxyGroup", b.langFormat("accessibility.drillUpButton", {
                    chart: b,
                    buttonText: b.getDrilldownBackText()
                }))
            },
            recreateProxyButtonAndGroup: function (b, c, a, d) {
                l(this[a]);
                this[a] = this.addProxyGroup();
                this[c] = this.createProxyButton(b, this[a], {
                    "aria-label": d,
                    tabindex: -1
                })
            },
            getMapZoomNavigation: function () {
                var b = this.keyCodes,
                    c = this.chart,
                    a = this;
                return new r(c, {
                    keyCodeMap: [
                        [
                            [b.up, b.down, b.left, b.right],
                            function (b) {
                                return a.onMapKbdArrow(this, b)
                            }
                        ],
                        [
                            [b.tab],
                            function (b, c) {
                                return a.onMapKbdTab(this, c)
                            }
                        ],
                        [
                            [b.space, b.enter],
                            function () {
                                return a.onMapKbdClick(this)
                            }
                        ]
                    ],
                    validate: function () {
                        return !!(c.mapZoom && c.mapNavButtons &&
                            c.mapNavButtons.length)
                    },
                    init: function (b) {
                        return a.onMapNavInit(b)
                    }
                })
            },
            onMapKbdArrow: function (b, c) {
                var a = this.keyCodes;
                this.chart[c === a.up || c === a.down ? "yAxis" : "xAxis"][0].panStep(c === a.left || c === a.up ? -1 : 1);
                return b.response.success
            },
            onMapKbdTab: function (b, c) {
                var a = this.chart;
                b = b.response;
                var d = (c = c.shiftKey) && !this.focusedMapNavButtonIx || !c && this.focusedMapNavButtonIx;
                a.mapNavButtons[this.focusedMapNavButtonIx].setState(0);
                if (d) return a.mapZoom(), b[c ? "prev" : "next"];
                this.focusedMapNavButtonIx += c ? -1 :
                    1;
                c = a.mapNavButtons[this.focusedMapNavButtonIx];
                a.setFocusToElement(c.box, c.element);
                c.setState(2);
                return b.success
            },
            onMapKbdClick: function (b) {
                this.fakeClickEvent(this.chart.mapNavButtons[this.focusedMapNavButtonIx].element);
                return b.response.success
            },
            onMapNavInit: function (b) {
                var c = this.chart,
                    a = c.mapNavButtons[0],
                    d = c.mapNavButtons[1];
                a = 0 < b ? a : d;
                c.setFocusToElement(a.box, a.element);
                a.setState(2);
                this.focusedMapNavButtonIx = 0 < b ? 0 : 1
            },
            simpleButtonNavigation: function (b, c, a) {
                var d = this.keyCodes,
                    f = this,
                    g =
                    this.chart;
                return new r(g, {
                    keyCodeMap: [
                        [
                            [d.tab, d.up, d.down, d.left, d.right],
                            function (a, b) {
                                return this.response[a === d.tab && b.shiftKey || a === d.left || a === d.up ? "prev" : "next"]
                            }
                        ],
                        [
                            [d.space, d.enter],
                            function () {
                                var b = a(this, g);
                                return p(b, this.response.success)
                            }
                        ]
                    ],
                    validate: function () {
                        return g[b] && g[b].box && f[c]
                    },
                    init: function () {
                        g.setFocusToElement(g[b].box, f[c])
                    }
                })
            },
            getKeyboardNavigation: function () {
                return [this.simpleButtonNavigation("resetZoomButton", "resetZoomProxyButton", function (b, c) {
                    c.zoomOut()
                }), this.simpleButtonNavigation("drillUpButton",
                    "drillUpProxyButton",
                    function (b, c) {
                        c.drillUp();
                        return b.response.prev
                    }), this.getMapZoomNavigation()]
            }
        });
        return e
    });
    v(b, "Extensions/RangeSelector.js", [b["Core/Axis/Axis.js"], b["Core/Chart/Chart.js"], b["Core/Globals.js"], b["Core/DefaultOptions.js"], b["Core/Color/Palette.js"], b["Core/Renderer/SVG/SVGElement.js"], b["Core/Utilities.js"]], function (b, e, q, n, r, x, m) {
        function l(a) {
            if (-1 !== a.indexOf("%L")) return "text";
            var b = "aAdewbBmoyY".split("").some(function (b) {
                    return -1 !== a.indexOf("%" + b)
                }),
                c = "HkIlMS".split("").some(function (b) {
                    return -1 !==
                        a.indexOf("%" + b)
                });
            return b && c ? "datetime-local" : b ? "date" : c ? "time" : "text"
        }
        var k = n.defaultOptions,
            p = m.addEvent,
            g = m.createElement,
            c = m.css,
            a = m.defined,
            d = m.destroyObjectProperties,
            f = m.discardElement,
            y = m.extend,
            G = m.find,
            C = m.fireEvent,
            w = m.isNumber,
            v = m.merge,
            I = m.objectEach,
            D = m.pad,
            z = m.pick,
            E = m.pInt,
            t = m.splat;
        y(k, {
            rangeSelector: {
                allButtonsEnabled: !1,
                buttons: void 0,
                buttonSpacing: 5,
                dropdown: "responsive",
                enabled: void 0,
                verticalAlign: "top",
                buttonTheme: {
                    width: 28,
                    height: 18,
                    padding: 2,
                    zIndex: 7
                },
                floating: !1,
                x: 0,
                y: 0,
                height: void 0,
                inputBoxBorderColor: "none",
                inputBoxHeight: 17,
                inputBoxWidth: void 0,
                inputDateFormat: "%b %e, %Y",
                inputDateParser: void 0,
                inputEditDateFormat: "%Y-%m-%d",
                inputEnabled: !0,
                inputPosition: {
                    align: "right",
                    x: 0,
                    y: 0
                },
                inputSpacing: 5,
                selected: void 0,
                buttonPosition: {
                    align: "left",
                    x: 0,
                    y: 0
                },
                inputStyle: {
                    color: r.highlightColor80,
                    cursor: "pointer"
                },
                labelStyle: {
                    color: r.neutralColor60
                }
            }
        });
        y(k.lang, {
            rangeSelectorZoom: "Zoom",
            rangeSelectorFrom: "",
            rangeSelectorTo: "\u2192"
        });
        var u = function () {
            function e(a) {
                this.buttons =
                    void 0;
                this.buttonOptions = e.prototype.defaultButtons;
                this.initialButtonGroupWidth = 0;
                this.options = void 0;
                this.chart = a;
                this.init(a)
            }
            e.prototype.clickButton = function (c, d) {
                var h = this.chart,
                    f = this.buttonOptions[c],
                    A = h.xAxis[0],
                    g = h.scroller && h.scroller.getUnionExtremes() || A || {},
                    e = g.dataMin,
                    k = g.dataMax,
                    l = A && Math.round(Math.min(A.max, z(k, A.max))),
                    m = f.type;
                g = f._range;
                var y, u = f.dataGrouping;
                if (null !== e && null !== k) {
                    h.fixedRange = g;
                    this.setSelected(c);
                    u && (this.forcedDataGrouping = !0, b.prototype.setDataGrouping.call(A || {
                        chart: this.chart
                    }, u, !1), this.frozenStates = f.preserveDataGrouping);
                    if ("month" === m || "year" === m)
                        if (A) {
                            m = {
                                range: f,
                                max: l,
                                chart: h,
                                dataMin: e,
                                dataMax: k
                            };
                            var n = A.minFromRange.call(m);
                            w(m.newMax) && (l = m.newMax)
                        } else g = f;
                    else if (g) n = Math.max(l - g, e), l = Math.min(n + g, k);
                    else if ("ytd" === m)
                        if (A) "undefined" === typeof k && (e = Number.MAX_VALUE, k = Number.MIN_VALUE, h.series.forEach(function (a) {
                            a = a.xData;
                            e = Math.min(a[0], e);
                            k = Math.max(a[a.length - 1], k)
                        }), d = !1), l = this.getYTDExtremes(k, e, h.time.useUTC), n = y = l.min, l = l.max;
                        else {
                            this.deferredYTDClick =
                                c;
                            return
                        }
                    else "all" === m && A && (h.navigator && h.navigator.baseSeries[0] && (h.navigator.baseSeries[0].xAxis.options.range = void 0), n = e, l = k);
                    a(n) && (n += f._offsetMin);
                    a(l) && (l += f._offsetMax);
                    this.dropdown && (this.dropdown.selectedIndex = c + 1);
                    if (A) A.setExtremes(n, l, z(d, !0), void 0, {
                        trigger: "rangeSelectorButton",
                        rangeSelectorButton: f
                    });
                    else {
                        var B = t(h.options.xAxis)[0];
                        var r = B.range;
                        B.range = g;
                        var q = B.min;
                        B.min = y;
                        p(h, "load", function () {
                            B.range = r;
                            B.min = q
                        })
                    }
                    C(this, "afterBtnClick")
                }
            };
            e.prototype.setSelected = function (a) {
                this.selected =
                    this.options.selected = a
            };
            e.prototype.init = function (a) {
                var b = this,
                    c = a.options.rangeSelector,
                    h = c.buttons || b.defaultButtons.slice(),
                    d = c.selected,
                    f = function () {
                        var a = b.minInput,
                            c = b.maxInput;
                        a && a.blur && C(a, "blur");
                        c && c.blur && C(c, "blur")
                    };
                b.chart = a;
                b.options = c;
                b.buttons = [];
                b.buttonOptions = h;
                this.eventsToUnbind = [];
                this.eventsToUnbind.push(p(a.container, "mousedown", f));
                this.eventsToUnbind.push(p(a, "resize", f));
                h.forEach(b.computeButtonRange);
                "undefined" !== typeof d && h[d] && this.clickButton(d, !1);
                this.eventsToUnbind.push(p(a,
                    "load",
                    function () {
                        a.xAxis && a.xAxis[0] && p(a.xAxis[0], "setExtremes", function (c) {
                            this.max - this.min !== a.fixedRange && "rangeSelectorButton" !== c.trigger && "updatedData" !== c.trigger && b.forcedDataGrouping && !b.frozenStates && this.setDataGrouping(!1, !1)
                        })
                    }))
            };
            e.prototype.updateButtonStates = function () {
                var a = this,
                    b = this.chart,
                    c = this.dropdown,
                    d = b.xAxis[0],
                    f = Math.round(d.max - d.min),
                    g = !d.hasVisibleSeries,
                    e = b.scroller && b.scroller.getUnionExtremes() || d,
                    k = e.dataMin,
                    t = e.dataMax;
                b = a.getYTDExtremes(t, k, b.time.useUTC);
                var l =
                    b.min,
                    p = b.max,
                    m = a.selected,
                    n = w(m),
                    B = a.options.allButtonsEnabled,
                    y = a.buttons;
                a.buttonOptions.forEach(function (b, h) {
                    var e = b._range,
                        A = b.type,
                        H = b.count || 1,
                        J = y[h],
                        M = 0,
                        u = b._offsetMax - b._offsetMin;
                    b = h === m;
                    var O = e > t - k,
                        r = e < d.minRange,
                        q = !1,
                        F = !1;
                    e = e === f;
                    ("month" === A || "year" === A) && f + 36E5 >= 864E5 * {
                        month: 28,
                        year: 365
                    } [A] * H - u && f - 36E5 <= 864E5 * {
                        month: 31,
                        year: 366
                    } [A] * H + u ? e = !0 : "ytd" === A ? (e = p - l + u === f, q = !b) : "all" === A && (e = d.max - d.min >= t - k, F = !b && n && e);
                    A = !B && (O || r || F || g);
                    H = b && e || e && !n && !q || b && a.frozenStates;
                    A ? M = 3 : H && (n = !0,
                        M = 2);
                    J.state !== M && (J.setState(M), c && (c.options[h + 1].disabled = A, 2 === M && (c.selectedIndex = h + 1)), 0 === M && m === h && a.setSelected())
                })
            };
            e.prototype.computeButtonRange = function (a) {
                var b = a.type,
                    c = a.count || 1,
                    d = {
                        millisecond: 1,
                        second: 1E3,
                        minute: 6E4,
                        hour: 36E5,
                        day: 864E5,
                        week: 6048E5
                    };
                if (d[b]) a._range = d[b] * c;
                else if ("month" === b || "year" === b) a._range = 864E5 * {
                    month: 30,
                    year: 365
                } [b] * c;
                a._offsetMin = z(a.offsetMin, 0);
                a._offsetMax = z(a.offsetMax, 0);
                a._range += a._offsetMax - a._offsetMin
            };
            e.prototype.getInputValue = function (a) {
                a =
                    "min" === a ? this.minInput : this.maxInput;
                var b = this.chart.options.rangeSelector,
                    c = this.chart.time;
                return a ? ("text" === a.type && b.inputDateParser || this.defaultInputDateParser)(a.value, c.useUTC, c) : 0
            };
            e.prototype.setInputValue = function (b, c) {
                var d = this.options,
                    h = this.chart.time,
                    f = "min" === b ? this.minInput : this.maxInput;
                b = "min" === b ? this.minDateBox : this.maxDateBox;
                if (f) {
                    var e = f.getAttribute("data-hc-time");
                    e = a(e) ? Number(e) : void 0;
                    a(c) && (a(e) && f.setAttribute("data-hc-time-previous", e), f.setAttribute("data-hc-time",
                        c), e = c);
                    f.value = h.dateFormat(this.inputTypeFormats[f.type] || d.inputEditDateFormat, e);
                    b && b.attr({
                        text: h.dateFormat(d.inputDateFormat, e)
                    })
                }
            };
            e.prototype.setInputExtremes = function (a, b, c) {
                if (a = "min" === a ? this.minInput : this.maxInput) {
                    var d = this.inputTypeFormats[a.type],
                        h = this.chart.time;
                    d && (b = h.dateFormat(d, b), a.min !== b && (a.min = b), c = h.dateFormat(d, c), a.max !== c && (a.max = c))
                }
            };
            e.prototype.showInput = function (a) {
                var b = "min" === a ? this.minDateBox : this.maxDateBox;
                if ((a = "min" === a ? this.minInput : this.maxInput) && b &&
                    this.inputGroup) {
                    var d = "text" === a.type,
                        h = this.inputGroup,
                        f = h.translateX;
                    h = h.translateY;
                    var e = this.options.inputBoxWidth;
                    c(a, {
                        width: d ? b.width + (e ? -2 : 20) + "px" : "auto",
                        height: d ? b.height - 2 + "px" : "auto",
                        border: "2px solid silver"
                    });
                    d && e ? c(a, {
                        left: f + b.x + "px",
                        top: h + "px"
                    }) : c(a, {
                        left: Math.min(Math.round(b.x + f - (a.offsetWidth - b.width) / 2), this.chart.chartWidth - a.offsetWidth) + "px",
                        top: h - (a.offsetHeight - b.height) / 2 + "px"
                    })
                }
            };
            e.prototype.hideInput = function (a) {
                (a = "min" === a ? this.minInput : this.maxInput) && c(a, {
                    top: "-9999em",
                    border: 0,
                    width: "1px",
                    height: "1px"
                })
            };
            e.prototype.defaultInputDateParser = function (a, b, c) {
                var d = a.split("/").join("-").split(" ").join("T"); - 1 === d.indexOf("T") && (d += "T00:00");
                if (b) d += "Z";
                else {
                    var h;
                    if (h = q.isSafari) h = d, h = !(6 < h.length && (h.lastIndexOf("-") === h.length - 6 || h.lastIndexOf("+") === h.length - 6));
                    h && (h = (new Date(d)).getTimezoneOffset() / 60, d += 0 >= h ? "+" + D(-h) + ":00" : "-" + D(h) + ":00")
                }
                d = Date.parse(d);
                w(d) || (a = a.split("-"), d = Date.UTC(E(a[0]), E(a[1]) - 1, E(a[2])));
                c && b && w(d) && (d += c.getTimezoneOffset(d));
                return d
            };
            e.prototype.drawInput = function (a) {
                function b() {
                    var b = e.getInputValue(a),
                        c = d.xAxis[0],
                        h = d.scroller && d.scroller.xAxis ? d.scroller.xAxis : c,
                        f = h.dataMin;
                    h = h.dataMax;
                    var g = e.maxInput,
                        k = e.minInput;
                    b !== Number(u.getAttribute("data-hc-time-previous")) && w(b) && (u.setAttribute("data-hc-time-previous", b), n && g && w(f) ? b > Number(g.getAttribute("data-hc-time")) ? b = void 0 : b < f && (b = f) : k && w(h) && (b < Number(k.getAttribute("data-hc-time")) ? b = void 0 : b > h && (b = h)), "undefined" !== typeof b && c.setExtremes(n ? b : c.min, n ? c.max : b, void 0, void 0, {
                        trigger: "rangeSelectorInput"
                    }))
                }
                var d = this.chart,
                    h = this.div,
                    f = this.inputGroup,
                    e = this,
                    t = d.renderer.style || {},
                    p = d.renderer,
                    m = d.options.rangeSelector,
                    n = "min" === a,
                    B = k.lang[n ? "rangeSelectorFrom" : "rangeSelectorTo"] || "";
                B = p.label(B, 0).addClass("highcharts-range-label").attr({
                    padding: B ? 2 : 0,
                    height: B ? m.inputBoxHeight : 0
                }).add(f);
                p = p.label("", 0).addClass("highcharts-range-input").attr({
                    padding: 2,
                    width: m.inputBoxWidth,
                    height: m.inputBoxHeight,
                    "text-align": "center"
                }).on("click", function () {
                    e.showInput(a);
                    e[a + "Input"].focus()
                });
                d.styledMode || p.attr({
                    stroke: m.inputBoxBorderColor,
                    "stroke-width": 1
                });
                p.add(f);
                var u = g("input", {
                    name: a,
                    className: "highcharts-range-selector"
                }, void 0, h);
                u.setAttribute("type", l(m.inputDateFormat || "%b %e, %Y"));
                d.styledMode || (B.css(v(t, m.labelStyle)), p.css(v({
                    color: r.neutralColor80
                }, t, m.inputStyle)), c(u, y({
                    position: "absolute",
                    border: 0,
                    boxShadow: "0 0 15px rgba(0,0,0,0.3)",
                    width: "1px",
                    height: "1px",
                    padding: 0,
                    textAlign: "center",
                    fontSize: t.fontSize,
                    fontFamily: t.fontFamily,
                    top: "-9999em"
                }, m.inputStyle)));
                u.onfocus = function () {
                    e.showInput(a)
                };
                u.onblur = function () {
                    u === q.doc.activeElement && b();
                    e.hideInput(a);
                    e.setInputValue(a);
                    u.blur()
                };
                var F = !1;
                u.onchange = function () {
                    F || (b(), e.hideInput(a), u.blur())
                };
                u.onkeypress = function (a) {
                    13 === a.keyCode && b()
                };
                u.onkeydown = function (a) {
                    F = !0;
                    38 !== a.keyCode && 40 !== a.keyCode || b()
                };
                u.onkeyup = function () {
                    F = !1
                };
                return {
                    dateBox: p,
                    input: u,
                    label: B
                }
            };
            e.prototype.getPosition = function () {
                var a = this.chart,
                    b = a.options.rangeSelector;
                a = "top" === b.verticalAlign ? a.plotTop - a.axisOffset[0] : 0;
                return {
                    buttonTop: a + b.buttonPosition.y,
                    inputTop: a + b.inputPosition.y - 10
                }
            };
            e.prototype.getYTDExtremes = function (a, b, c) {
                var d = this.chart.time,
                    h = new d.Date(a),
                    f = d.get("FullYear", h);
                c = c ? d.Date.UTC(f, 0, 1) : +new d.Date(f, 0, 1);
                b = Math.max(b, c);
                h = h.getTime();
                return {
                    max: Math.min(a || h, h),
                    min: b
                }
            };
            e.prototype.render = function (b, c) {
                var d = this.chart,
                    h = d.renderer,
                    f = d.container,
                    e = d.options,
                    k = e.rangeSelector,
                    t = z(e.chart.style && e.chart.style.zIndex, 0) + 1;
                e = k.inputEnabled;
                if (!1 !== k.enabled) {
                    this.rendered || (this.group = h.g("range-selector-group").attr({
                            zIndex: 7
                        }).add(),
                        this.div = g("div", void 0, {
                            position: "relative",
                            height: 0,
                            zIndex: t
                        }), this.buttonOptions.length && this.renderButtons(), f.parentNode && f.parentNode.insertBefore(this.div, f), e && (this.inputGroup = h.g("input-group").add(this.group), h = this.drawInput("min"), this.minDateBox = h.dateBox, this.minLabel = h.label, this.minInput = h.input, h = this.drawInput("max"), this.maxDateBox = h.dateBox, this.maxLabel = h.label, this.maxInput = h.input));
                    if (e && (this.setInputValue("min", b), this.setInputValue("max", c), b = d.scroller && d.scroller.getUnionExtremes() ||
                            d.xAxis[0] || {}, a(b.dataMin) && a(b.dataMax) && (d = d.xAxis[0].minRange || 0, this.setInputExtremes("min", b.dataMin, Math.min(b.dataMax, this.getInputValue("max")) - d), this.setInputExtremes("max", Math.max(b.dataMin, this.getInputValue("min")) + d, b.dataMax)), this.inputGroup)) {
                        var p = 0;
                        [this.minLabel, this.minDateBox, this.maxLabel, this.maxDateBox].forEach(function (a) {
                            if (a) {
                                var b = a.getBBox().width;
                                b && (a.attr({
                                    x: p
                                }), p += b + k.inputSpacing)
                            }
                        })
                    }
                    this.alignElements();
                    this.rendered = !0
                }
            };
            e.prototype.renderButtons = function () {
                var a =
                    this,
                    b = this.buttons,
                    c = this.options,
                    d = k.lang,
                    f = this.chart.renderer,
                    e = v(c.buttonTheme),
                    t = e && e.states,
                    l = e.width || 28;
                delete e.width;
                delete e.states;
                this.buttonGroup = f.g("range-selector-buttons").add(this.group);
                var m = this.dropdown = g("select", void 0, {
                    position: "absolute",
                    width: "1px",
                    height: "1px",
                    padding: 0,
                    border: 0,
                    top: "-9999em",
                    cursor: "pointer",
                    opacity: .0001
                }, this.div);
                p(m, "touchstart", function () {
                    m.style.fontSize = "16px"
                });
                [
                    [q.isMS ? "mouseover" : "mouseenter"],
                    [q.isMS ? "mouseout" : "mouseleave"],
                    ["change", "click"]
                ].forEach(function (c) {
                    var d =
                        c[0],
                        h = c[1];
                    p(m, d, function () {
                        var c = b[a.currentButtonIndex()];
                        c && C(c.element, h || d)
                    })
                });
                this.zoomText = f.label(d && d.rangeSelectorZoom || "", 0).attr({
                    padding: c.buttonTheme.padding,
                    height: c.buttonTheme.height,
                    paddingLeft: 0,
                    paddingRight: 0
                }).add(this.buttonGroup);
                this.chart.styledMode || (this.zoomText.css(c.labelStyle), e["stroke-width"] = z(e["stroke-width"], 0));
                g("option", {
                    textContent: this.zoomText.textStr,
                    disabled: !0
                }, void 0, m);
                this.buttonOptions.forEach(function (c, d) {
                    g("option", {
                            textContent: c.title || c.text
                        },
                        void 0, m);
                    b[d] = f.button(c.text, 0, 0, function (b) {
                        var h = c.events && c.events.click,
                            f;
                        h && (f = h.call(c, b));
                        !1 !== f && a.clickButton(d);
                        a.isActive = !0
                    }, e, t && t.hover, t && t.select, t && t.disabled).attr({
                        "text-align": "center",
                        width: l
                    }).add(a.buttonGroup);
                    c.title && b[d].attr("title", c.title)
                })
            };
            e.prototype.alignElements = function () {
                var a = this,
                    b = this.buttonGroup,
                    c = this.buttons,
                    d = this.chart,
                    f = this.group,
                    e = this.inputGroup,
                    g = this.options,
                    k = this.zoomText,
                    t = d.options,
                    p = t.exporting && !1 !== t.exporting.enabled && t.navigation && t.navigation.buttonOptions;
                t = g.buttonPosition;
                var l = g.inputPosition,
                    m = g.verticalAlign,
                    u = function (b, c) {
                        return p && a.titleCollision(d) && "top" === m && "right" === c.align && c.y - b.getBBox().height - 12 < (p.y || 0) + (p.height || 0) + d.spacing[0] ? -40 : 0
                    },
                    n = d.plotLeft;
                if (f && t && l) {
                    var B = t.x - d.spacing[3];
                    if (b) {
                        this.positionButtons();
                        if (!this.initialButtonGroupWidth) {
                            var y = 0;
                            k && (y += k.getBBox().width + 5);
                            c.forEach(function (a, b) {
                                y += a.width;
                                b !== c.length - 1 && (y += g.buttonSpacing)
                            });
                            this.initialButtonGroupWidth = y
                        }
                        n -= d.spacing[3];
                        this.updateButtonStates();
                        k =
                            u(b, t);
                        this.alignButtonGroup(k);
                        f.placed = b.placed = d.hasLoaded
                    }
                    b = 0;
                    e && (b = u(e, l), "left" === l.align ? B = n : "right" === l.align && (B = -Math.max(d.axisOffset[1], -b)), e.align({
                        y: l.y,
                        width: e.getBBox().width,
                        align: l.align,
                        x: l.x + B - 2
                    }, !0, d.spacingBox), e.placed = d.hasLoaded);
                    this.handleCollision(b);
                    f.align({
                        verticalAlign: m
                    }, !0, d.spacingBox);
                    e = f.alignAttr.translateY;
                    b = f.getBBox().height + 20;
                    u = 0;
                    "bottom" === m && (u = (u = d.legend && d.legend.options) && "bottom" === u.verticalAlign && u.enabled && !u.floating ? d.legend.legendHeight + z(u.margin,
                        10) : 0, b = b + u - 20, u = e - b - (g.floating ? 0 : g.y) - (d.titleOffset ? d.titleOffset[2] : 0) - 10);
                    if ("top" === m) g.floating && (u = 0), d.titleOffset && d.titleOffset[0] && (u = d.titleOffset[0]), u += d.margin[0] - d.spacing[0] || 0;
                    else if ("middle" === m)
                        if (l.y === t.y) u = e;
                        else if (l.y || t.y) u = 0 > l.y || 0 > t.y ? u - Math.min(l.y, t.y) : e - b;
                    f.translate(g.x, g.y + Math.floor(u));
                    t = this.minInput;
                    l = this.maxInput;
                    e = this.dropdown;
                    g.inputEnabled && t && l && (t.style.marginTop = f.translateY + "px", l.style.marginTop = f.translateY + "px");
                    e && (e.style.marginTop = f.translateY +
                        "px")
                }
            };
            e.prototype.alignButtonGroup = function (a, b) {
                var c = this.chart,
                    d = this.buttonGroup,
                    h = this.options.buttonPosition,
                    f = c.plotLeft - c.spacing[3],
                    e = h.x - c.spacing[3];
                "right" === h.align ? e += a - f : "center" === h.align && (e -= f / 2);
                d && d.align({
                    y: h.y,
                    width: z(b, this.initialButtonGroupWidth),
                    align: h.align,
                    x: e
                }, !0, c.spacingBox)
            };
            e.prototype.positionButtons = function () {
                var a = this.buttons,
                    b = this.chart,
                    c = this.options,
                    d = this.zoomText,
                    f = b.hasLoaded ? "animate" : "attr",
                    e = c.buttonPosition,
                    g = b.plotLeft,
                    k = g;
                d && "hidden" !== d.visibility &&
                    (d[f]({
                        x: z(g + e.x, g)
                    }), k += e.x + d.getBBox().width + 5);
                this.buttonOptions.forEach(function (b, d) {
                    if ("hidden" !== a[d].visibility) a[d][f]({
                        x: k
                    }), k += a[d].width + c.buttonSpacing;
                    else a[d][f]({
                        x: g
                    })
                })
            };
            e.prototype.handleCollision = function (a) {
                var b = this,
                    c = this.chart,
                    d = this.buttonGroup,
                    h = this.inputGroup,
                    f = this.options,
                    e = f.buttonPosition,
                    g = f.dropdown,
                    k = f.inputPosition;
                f = function () {
                    var a = 0;
                    b.buttons.forEach(function (b) {
                        b = b.getBBox();
                        b.width > a && (a = b.width)
                    });
                    return a
                };
                var t = function (b) {
                        if (h && d) {
                            var c = h.alignAttr.translateX +
                                h.alignOptions.x - a + h.getBBox().x + 2,
                                f = h.alignOptions.width,
                                g = d.alignAttr.translateX + d.getBBox().x;
                            return g + b > c && c + f > g && e.y < k.y + h.getBBox().height
                        }
                        return !1
                    },
                    l = function () {
                        h && d && h.attr({
                            translateX: h.alignAttr.translateX + (c.axisOffset[1] >= -a ? 0 : -a),
                            translateY: h.alignAttr.translateY + d.getBBox().height + 10
                        })
                    };
                if (d) {
                    if ("always" === g) {
                        this.collapseButtons(a);
                        t(f()) && l();
                        return
                    }
                    "never" === g && this.expandButtons()
                }
                h && d ? k.align === e.align || t(this.initialButtonGroupWidth + 20) ? "responsive" === g ? (this.collapseButtons(a),
                    t(f()) && l()) : l() : "responsive" === g && this.expandButtons() : d && "responsive" === g && (this.initialButtonGroupWidth > c.plotWidth ? this.collapseButtons(a) : this.expandButtons())
            };
            e.prototype.collapseButtons = function (a) {
                var b = this.buttons,
                    c = this.buttonOptions,
                    d = this.chart,
                    h = this.dropdown,
                    f = this.options,
                    e = this.zoomText,
                    g = d.userOptions.rangeSelector && d.userOptions.rangeSelector.buttonTheme || {},
                    k = function (a) {
                        return {
                            text: a ? a + " \u25be" : "\u25be",
                            width: "auto",
                            paddingLeft: z(f.buttonTheme.paddingLeft, g.padding, 8),
                            paddingRight: z(f.buttonTheme.paddingRight,
                                g.padding, 8)
                        }
                    };
                e && e.hide();
                var t = !1;
                c.forEach(function (a, c) {
                    c = b[c];
                    2 !== c.state ? c.hide() : (c.show(), c.attr(k(a.text)), t = !0)
                });
                t || (h && (h.selectedIndex = 0), b[0].show(), b[0].attr(k(this.zoomText && this.zoomText.textStr)));
                c = f.buttonPosition.align;
                this.positionButtons();
                "right" !== c && "center" !== c || this.alignButtonGroup(a, b[this.currentButtonIndex()].getBBox().width);
                this.showDropdown()
            };
            e.prototype.expandButtons = function () {
                var a = this.buttons,
                    b = this.buttonOptions,
                    c = this.options,
                    d = this.zoomText;
                this.hideDropdown();
                d && d.show();
                b.forEach(function (b, d) {
                    d = a[d];
                    d.show();
                    d.attr({
                        text: b.text,
                        width: c.buttonTheme.width || 28,
                        paddingLeft: z(c.buttonTheme.paddingLeft, "unset"),
                        paddingRight: z(c.buttonTheme.paddingRight, "unset")
                    });
                    2 > d.state && d.setState(0)
                });
                this.positionButtons()
            };
            e.prototype.currentButtonIndex = function () {
                var a = this.dropdown;
                return a && 0 < a.selectedIndex ? a.selectedIndex - 1 : 0
            };
            e.prototype.showDropdown = function () {
                var a = this.buttonGroup,
                    b = this.buttons,
                    d = this.chart,
                    f = this.dropdown;
                if (a && f) {
                    var e = a.translateX;
                    a = a.translateY;
                    b = b[this.currentButtonIndex()].getBBox();
                    c(f, {
                        left: d.plotLeft + e + "px",
                        top: a + .5 + "px",
                        width: b.width + "px",
                        height: b.height + "px"
                    });
                    this.hasVisibleDropdown = !0
                }
            };
            e.prototype.hideDropdown = function () {
                var a = this.dropdown;
                a && (c(a, {
                    top: "-9999em",
                    width: "1px",
                    height: "1px"
                }), this.hasVisibleDropdown = !1)
            };
            e.prototype.getHeight = function () {
                var a = this.options,
                    b = this.group,
                    c = a.y,
                    d = a.buttonPosition.y,
                    f = a.inputPosition.y;
                if (a.height) return a.height;
                this.alignElements();
                a = b ? b.getBBox(!0).height + 13 + c : 0;
                b = Math.min(f, d);
                if (0 >
                    f && 0 > d || 0 < f && 0 < d) a += Math.abs(b);
                return a
            };
            e.prototype.titleCollision = function (a) {
                return !(a.options.title.text || a.options.subtitle.text)
            };
            e.prototype.update = function (a) {
                var b = this.chart;
                v(!0, b.options.rangeSelector, a);
                this.destroy();
                this.init(b);
                this.render()
            };
            e.prototype.destroy = function () {
                var a = this,
                    b = a.minInput,
                    c = a.maxInput;
                a.eventsToUnbind && (a.eventsToUnbind.forEach(function (a) {
                    return a()
                }), a.eventsToUnbind = void 0);
                d(a.buttons);
                b && (b.onfocus = b.onblur = b.onchange = null);
                c && (c.onfocus = c.onblur = c.onchange =
                    null);
                I(a, function (b, c) {
                    b && "chart" !== c && (b instanceof x ? b.destroy() : b instanceof window.HTMLElement && f(b));
                    b !== e.prototype[c] && (a[c] = null)
                }, this)
            };
            return e
        }();
        u.prototype.defaultButtons = [{
            type: "month",
            count: 1,
            text: "1m",
            title: "View 1 month"
        }, {
            type: "month",
            count: 3,
            text: "3m",
            title: "View 3 months"
        }, {
            type: "month",
            count: 6,
            text: "6m",
            title: "View 6 months"
        }, {
            type: "ytd",
            text: "YTD",
            title: "View year to date"
        }, {
            type: "year",
            count: 1,
            text: "1y",
            title: "View 1 year"
        }, {
            type: "all",
            text: "All",
            title: "View all"
        }];
        u.prototype.inputTypeFormats = {
            "datetime-local": "%Y-%m-%dT%H:%M:%S",
            date: "%Y-%m-%d",
            time: "%H:%M:%S"
        };
        b.prototype.minFromRange = function () {
            var a = this.range,
                b = a.type,
                c = this.max,
                d = this.chart.time,
                f = function (a, c) {
                    var f = "year" === b ? "FullYear" : "Month",
                        e = new d.Date(a),
                        h = d.get(f, e);
                    d.set(f, e, h + c);
                    h === d.get(f, e) && d.set("Date", e, 0);
                    return e.getTime() - a
                };
            if (w(a)) {
                var e = c - a;
                var g = a
            } else e = c + f(c, -a.count), this.chart && (this.chart.fixedRange = c - e);
            var k = z(this.dataMin, Number.MIN_VALUE);
            w(e) || (e = k);
            e <= k && (e = k, "undefined" === typeof g && (g = f(e, a.count)),
                this.newMax = Math.min(e + g, this.dataMax));
            w(c) || (e = void 0);
            return e
        };
        if (!q.RangeSelector) {
            var B = [],
                K = function (a) {
                    function b() {
                        d && (c = a.xAxis[0].getExtremes(), f = a.legend, g = d && d.options.verticalAlign, w(c.min) && d.render(c.min, c.max), f.display && "top" === g && g === f.options.verticalAlign && (e = v(a.spacingBox), e.y = "vertical" === f.options.layout ? a.plotTop : e.y + d.getHeight(), f.group.placed = !1, f.align(e)))
                    }
                    var c, d = a.rangeSelector,
                        f, e, g;
                    d && (G(B, function (b) {
                        return b[0] === a
                    }) || B.push([a, [p(a.xAxis[0], "afterSetExtremes",
                        function (a) {
                            d && d.render(a.min, a.max)
                        }), p(a, "redraw", b)]]), b())
                };
            p(e, "afterGetContainer", function () {
                this.options.rangeSelector && this.options.rangeSelector.enabled && (this.rangeSelector = new u(this))
            });
            p(e, "beforeRender", function () {
                var a = this.axes,
                    b = this.rangeSelector;
                b && (w(b.deferredYTDClick) && (b.clickButton(b.deferredYTDClick), delete b.deferredYTDClick), a.forEach(function (a) {
                    a.updateNames();
                    a.setScale()
                }), this.getAxisMargins(), b.render(), a = b.options.verticalAlign, b.options.floating || ("bottom" === a ? this.extraBottomMargin = !0 : "middle" !== a && (this.extraTopMargin = !0)))
            });
            p(e, "update", function (b) {
                var c = b.options.rangeSelector;
                b = this.rangeSelector;
                var d = this.extraBottomMargin,
                    f = this.extraTopMargin;
                c && c.enabled && !a(b) && this.options.rangeSelector && (this.options.rangeSelector.enabled = !0, this.rangeSelector = b = new u(this));
                this.extraTopMargin = this.extraBottomMargin = !1;
                b && (K(this), c = c && c.verticalAlign || b.options && b.options.verticalAlign, b.options.floating || ("bottom" === c ? this.extraBottomMargin = !0 : "middle" !== c && (this.extraTopMargin = !0)), this.extraBottomMargin !== d || this.extraTopMargin !== f) && (this.isDirtyBox = !0)
            });
            p(e, "render", function () {
                var a = this.rangeSelector;
                a && !a.options.floating && (a.render(), a = a.options.verticalAlign, "bottom" === a ? this.extraBottomMargin = !0 : "middle" !== a && (this.extraTopMargin = !0))
            });
            p(e, "getMargins", function () {
                var a = this.rangeSelector;
                a && (a = a.getHeight(), this.extraTopMargin && (this.plotTop += a), this.extraBottomMargin && (this.marginBottom += a))
            });
            e.prototype.callbacks.push(K);
            p(e, "destroy", function () {
                for (var a =
                        0; a < B.length; a++) {
                    var b = B[a];
                    if (b[0] === this) {
                        b[1].forEach(function (a) {
                            return a()
                        });
                        B.splice(a, 1);
                        break
                    }
                }
            });
            q.RangeSelector = u
        }
        return u
    });
    v(b, "Accessibility/Components/RangeSelectorComponent.js", [b["Accessibility/AccessibilityComponent.js"], b["Accessibility/Utils/ChartUtilities.js"], b["Accessibility/Utils/Announcer.js"], b["Core/Chart/Chart.js"], b["Accessibility/Utils/HTMLUtilities.js"], b["Accessibility/KeyboardNavigationHandler.js"], b["Core/Utilities.js"], b["Extensions/RangeSelector.js"]], function (b,
        e, q, n, r, x, m, l) {
        var k = e.unhideChartElementFromAT,
            p = e.getAxisRangeDescription,
            g = r.setElAttrs,
            c = m.addEvent;
        e = m.extend;
        n.prototype.highlightRangeSelectorButton = function (a) {
            var b = this.rangeSelector && this.rangeSelector.buttons || [],
                c = this.highlightedRangeSelectorItemIx,
                e = this.rangeSelector && this.rangeSelector.selected;
            "undefined" !== typeof c && b[c] && c !== e && b[c].setState(this.oldRangeSelectorItemState || 0);
            this.highlightedRangeSelectorItemIx = a;
            return b[a] ? (this.setFocusToElement(b[a].box, b[a].element), a !== e &&
                (this.oldRangeSelectorItemState = b[a].state, b[a].setState(1)), !0) : !1
        };
        c(l, "afterBtnClick", function () {
            if (this.chart.accessibility && this.chart.accessibility.components.rangeSelector) return this.chart.accessibility.components.rangeSelector.onAfterBtnClick()
        });
        n = function () {};
        n.prototype = new b;
        e(n.prototype, {
            init: function () {
                this.announcer = new q(this.chart, "polite")
            },
            onChartUpdate: function () {
                var a = this.chart,
                    b = this,
                    c = a.rangeSelector;
                c && (this.updateSelectorVisibility(), this.setDropdownAttrs(), c.buttons &&
                    c.buttons.length && c.buttons.forEach(function (a) {
                        b.setRangeButtonAttrs(a)
                    }), c.maxInput && c.minInput && ["minInput", "maxInput"].forEach(function (d, f) {
                        if (d = c[d]) k(a, d), b.setRangeInputAttrs(d, "accessibility.rangeSelector." + (f ? "max" : "min") + "InputLabel")
                    }))
            },
            updateSelectorVisibility: function () {
                var a = this.chart,
                    b = a.rangeSelector,
                    c = b && b.dropdown,
                    e = b && b.buttons || [];
                b && b.hasVisibleDropdown && c ? (k(a, c), e.forEach(function (a) {
                    return a.element.setAttribute("aria-hidden", !0)
                })) : (c && c.setAttribute("aria-hidden", !0),
                    e.forEach(function (b) {
                        return k(a, b.element)
                    }))
            },
            setDropdownAttrs: function () {
                var a = this.chart,
                    b = a.rangeSelector && a.rangeSelector.dropdown;
                b && (a = a.langFormat("accessibility.rangeSelector.dropdownLabel", {
                    rangeTitle: a.options.lang.rangeSelectorZoom
                }), b.setAttribute("aria-label", a), b.setAttribute("tabindex", -1))
            },
            setRangeButtonAttrs: function (a) {
                g(a.element, {
                    tabindex: -1,
                    role: "button"
                })
            },
            setRangeInputAttrs: function (a, b) {
                var c = this.chart;
                g(a, {
                    tabindex: -1,
                    "aria-label": c.langFormat(b, {
                        chart: c
                    })
                })
            },
            onButtonNavKbdArrowKey: function (a,
                b) {
                var c = a.response,
                    d = this.keyCodes,
                    e = this.chart,
                    g = e.options.accessibility.keyboardNavigation.wrapAround;
                b = b === d.left || b === d.up ? -1 : 1;
                return e.highlightRangeSelectorButton(e.highlightedRangeSelectorItemIx + b) ? c.success : g ? (a.init(b), c.success) : c[0 < b ? "next" : "prev"]
            },
            onButtonNavKbdClick: function (a) {
                a = a.response;
                var b = this.chart;
                3 !== b.oldRangeSelectorItemState && this.fakeClickEvent(b.rangeSelector.buttons[b.highlightedRangeSelectorItemIx].element);
                return a.success
            },
            onAfterBtnClick: function () {
                var a = this.chart,
                    b = p(a.xAxis[0]);
                (a = a.langFormat("accessibility.rangeSelector.clickButtonAnnouncement", {
                    chart: a,
                    axisRangeDescription: b
                })) && this.announcer.announce(a)
            },
            onInputKbdMove: function (a) {
                var b = this.chart,
                    c = b.rangeSelector,
                    e = b.highlightedInputRangeIx = (b.highlightedInputRangeIx || 0) + a;
                1 < e || 0 > e ? b.accessibility && (b.accessibility.keyboardNavigation.tabindexContainer.focus(), b.accessibility.keyboardNavigation[0 > a ? "prev" : "next"]()) : c && (a = c[e ? "maxDateBox" : "minDateBox"], c = c[e ? "maxInput" : "minInput"], a && c && b.setFocusToElement(a,
                    c))
            },
            onInputNavInit: function (a) {
                var b = this,
                    e = this,
                    g = this.chart,
                    k = 0 < a ? 0 : 1,
                    l = g.rangeSelector,
                    p = l && l[k ? "maxDateBox" : "minDateBox"];
                a = l && l.minInput;
                l = l && l.maxInput;
                g.highlightedInputRangeIx = k;
                if (p && a && l) {
                    g.setFocusToElement(p, k ? l : a);
                    this.removeInputKeydownHandler && this.removeInputKeydownHandler();
                    g = function (a) {
                        (a.which || a.keyCode) === b.keyCodes.tab && (a.preventDefault(), a.stopPropagation(), e.onInputKbdMove(a.shiftKey ? -1 : 1))
                    };
                    var m = c(a, "keydown", g),
                        n = c(l, "keydown", g);
                    this.removeInputKeydownHandler = function () {
                        m();
                        n()
                    }
                }
            },
            onInputNavTerminate: function () {
                var a = this.chart.rangeSelector || {};
                a.maxInput && a.hideInput("max");
                a.minInput && a.hideInput("min");
                this.removeInputKeydownHandler && (this.removeInputKeydownHandler(), delete this.removeInputKeydownHandler)
            },
            initDropdownNav: function () {
                var a = this,
                    b = this.chart,
                    e = b.rangeSelector,
                    g = e && e.dropdown;
                e && g && (b.setFocusToElement(e.buttonGroup, g), this.removeDropdownKeydownHandler && this.removeDropdownKeydownHandler(), this.removeDropdownKeydownHandler = c(g, "keydown", function (c) {
                    (c.which ||
                        c.keyCode) === a.keyCodes.tab && (c.preventDefault(), c.stopPropagation(), b.accessibility && (b.accessibility.keyboardNavigation.tabindexContainer.focus(), b.accessibility.keyboardNavigation[c.shiftKey ? "prev" : "next"]()))
                }))
            },
            getRangeSelectorButtonNavigation: function () {
                var a = this.chart,
                    b = this.keyCodes,
                    c = this;
                return new x(a, {
                    keyCodeMap: [
                        [
                            [b.left, b.right, b.up, b.down],
                            function (a) {
                                return c.onButtonNavKbdArrowKey(this, a)
                            }
                        ],
                        [
                            [b.enter, b.space],
                            function () {
                                return c.onButtonNavKbdClick(this)
                            }
                        ]
                    ],
                    validate: function () {
                        return !!(a.rangeSelector &&
                            a.rangeSelector.buttons && a.rangeSelector.buttons.length)
                    },
                    init: function (b) {
                        var d = a.rangeSelector;
                        d && d.hasVisibleDropdown ? c.initDropdownNav() : d && (d = d.buttons.length - 1, a.highlightRangeSelectorButton(0 < b ? 0 : d))
                    },
                    terminate: function () {
                        c.removeDropdownKeydownHandler && (c.removeDropdownKeydownHandler(), delete c.removeDropdownKeydownHandler)
                    }
                })
            },
            getRangeSelectorInputNavigation: function () {
                var a = this.chart,
                    b = this;
                return new x(a, {
                    keyCodeMap: [],
                    validate: function () {
                        return !!(a.rangeSelector && a.rangeSelector.inputGroup &&
                            "hidden" !== a.rangeSelector.inputGroup.element.getAttribute("visibility") && !1 !== a.options.rangeSelector.inputEnabled && a.rangeSelector.minInput && a.rangeSelector.maxInput)
                    },
                    init: function (a) {
                        b.onInputNavInit(a)
                    },
                    terminate: function () {
                        b.onInputNavTerminate()
                    }
                })
            },
            getKeyboardNavigation: function () {
                return [this.getRangeSelectorButtonNavigation(), this.getRangeSelectorInputNavigation()]
            },
            destroy: function () {
                this.removeDropdownKeydownHandler && this.removeDropdownKeydownHandler();
                this.removeInputKeydownHandler && this.removeInputKeydownHandler();
                this.announcer && this.announcer.destroy()
            }
        });
        return n
    });
    v(b, "Accessibility/Components/InfoRegionsComponent.js", [b["Core/Renderer/HTML/AST.js"], b["Core/Chart/Chart.js"], b["Core/FormatUtilities.js"], b["Core/Globals.js"], b["Core/Utilities.js"], b["Accessibility/AccessibilityComponent.js"], b["Accessibility/Utils/Announcer.js"], b["Accessibility/Components/AnnotationsA11y.js"], b["Accessibility/Utils/ChartUtilities.js"], b["Accessibility/Utils/HTMLUtilities.js"]], function (b, e, q, n, r, x, m, l, k, p) {
        var g = q.format,
            c = n.doc;
        q = r.extend;
        var a = r.pick,
            d = l.getAnnotationsInfoHTML,
            f = k.getAxisDescription,
            y = k.getAxisRangeDescription,
            w = k.getChartTitle,
            v = k.unhideChartElementFromAT,
            L = p.addClass,
            N = p.getElement,
            I = p.getHeadingTagNameForElement,
            D = p.setElAttrs,
            z = p.stripHTMLTagsFromString,
            E = p.visuallyHideElement;
        e.prototype.getTypeDescription = function (a) {
            var b = a[0],
                c = this.series && this.series[0] || {};
            c = {
                numSeries: this.series.length,
                numPoints: c.points && c.points.length,
                chart: this,
                mapTitle: c.mapTitle
            };
            if (!b) return this.langFormat("accessibility.chartTypes.emptyChart",
                c);
            if ("map" === b) return c.mapTitle ? this.langFormat("accessibility.chartTypes.mapTypeDescription", c) : this.langFormat("accessibility.chartTypes.unknownMap", c);
            if (1 < this.types.length) return this.langFormat("accessibility.chartTypes.combinationChart", c);
            a = a[0];
            b = this.langFormat("accessibility.seriesTypeDescriptions." + a, c);
            var d = this.series && 2 > this.series.length ? "Single" : "Multiple";
            return (this.langFormat("accessibility.chartTypes." + a + d, c) || this.langFormat("accessibility.chartTypes.default" + d, c)) + (b ? " " +
                b : "")
        };
        e = function () {};
        e.prototype = new x;
        q(e.prototype, {
            init: function () {
                var a = this.chart,
                    b = this;
                this.initRegionsDefinitions();
                this.addEvent(a, "aftergetTableAST", function (a) {
                    b.onDataTableCreated(a)
                });
                this.addEvent(a, "afterViewData", function (a) {
                    b.dataTableDiv = a;
                    setTimeout(function () {
                        b.focusDataTable()
                    }, 300)
                });
                this.announcer = new m(a, "assertive")
            },
            initRegionsDefinitions: function () {
                var a = this;
                this.screenReaderSections = {
                    before: {
                        element: null,
                        buildContent: function (b) {
                            var c = b.options.accessibility.screenReaderSection.beforeChartFormatter;
                            return c ? c(b) : a.defaultBeforeChartFormatter(b)
                        },
                        insertIntoDOM: function (a, b) {
                            b.renderTo.insertBefore(a, b.renderTo.firstChild)
                        },
                        afterInserted: function () {
                            "undefined" !== typeof a.sonifyButtonId && a.initSonifyButton(a.sonifyButtonId);
                            "undefined" !== typeof a.dataTableButtonId && a.initDataTableButton(a.dataTableButtonId)
                        }
                    },
                    after: {
                        element: null,
                        buildContent: function (b) {
                            var c = b.options.accessibility.screenReaderSection.afterChartFormatter;
                            return c ? c(b) : a.defaultAfterChartFormatter()
                        },
                        insertIntoDOM: function (a, b) {
                            b.renderTo.insertBefore(a,
                                b.container.nextSibling)
                        },
                        afterInserted: function () {
                            a.chart.accessibility && a.chart.accessibility.keyboardNavigation.updateExitAnchor()
                        }
                    }
                }
            },
            onChartRender: function () {
                var a = this;
                this.linkedDescriptionElement = this.getLinkedDescriptionElement();
                this.setLinkedDescriptionAttrs();
                Object.keys(this.screenReaderSections).forEach(function (b) {
                    a.updateScreenReaderSection(b)
                })
            },
            getLinkedDescriptionElement: function () {
                var a = this.chart.options.accessibility.linkedDescription;
                if (a) {
                    if ("string" !== typeof a) return a;
                    a =
                        g(a, this.chart);
                    a = c.querySelectorAll(a);
                    if (1 === a.length) return a[0]
                }
            },
            setLinkedDescriptionAttrs: function () {
                var a = this.linkedDescriptionElement;
                a && (a.setAttribute("aria-hidden", "true"), L(a, "highcharts-linked-description"))
            },
            updateScreenReaderSection: function (a) {
                var c = this.chart,
                    d = this.screenReaderSections[a],
                    e = d.buildContent(c),
                    f = d.element = d.element || this.createElement("div"),
                    h = f.firstChild || this.createElement("div");
                this.setScreenReaderSectionAttribs(f, a);
                b.setElementHTML(h, e);
                f.appendChild(h);
                d.insertIntoDOM(f,
                    c);
                E(h);
                v(c, h);
                d.afterInserted && d.afterInserted()
            },
            setScreenReaderSectionAttribs: function (a, b) {
                var c = this.chart,
                    d = c.langFormat("accessibility.screenReaderSection." + b + "RegionLabel", {
                        chart: c,
                        chartTitle: w(c)
                    });
                D(a, {
                    id: "highcharts-screen-reader-region-" + b + "-" + c.index,
                    "aria-label": d
                });
                a.style.position = "relative";
                "all" === c.options.accessibility.landmarkVerbosity && d && a.setAttribute("role", "region")
            },
            defaultBeforeChartFormatter: function () {
                var a = this.chart,
                    b = a.options.accessibility.screenReaderSection.beforeChartFormat,
                    c = this.getAxesDescription(),
                    e = a.sonify && a.options.sonification && a.options.sonification.enabled,
                    f = "highcharts-a11y-sonify-data-btn-" + a.index,
                    h = "hc-linkto-highcharts-data-table-" + a.index,
                    g = d(a),
                    k = a.langFormat("accessibility.screenReaderSection.annotations.heading", {
                        chart: a
                    });
                c = {
                    headingTagName: I(a.renderTo),
                    chartTitle: w(a),
                    typeDescription: this.getTypeDescriptionText(),
                    chartSubtitle: this.getSubtitleText(),
                    chartLongdesc: this.getLongdescText(),
                    xAxisDescription: c.xAxis,
                    yAxisDescription: c.yAxis,
                    playAsSoundButton: e ?
                        this.getSonifyButtonText(f) : "",
                    viewTableButton: a.getCSV ? this.getDataTableButtonText(h) : "",
                    annotationsTitle: g ? k : "",
                    annotationsList: g
                };
                a = n.i18nFormat(b, c, a);
                this.dataTableButtonId = h;
                this.sonifyButtonId = f;
                return a.replace(/<(\w+)[^>]*?>\s*<\/\1>/g, "")
            },
            defaultAfterChartFormatter: function () {
                var a = this.chart,
                    b = a.options.accessibility.screenReaderSection.afterChartFormat,
                    c = {
                        endOfChartMarker: this.getEndOfChartMarkerText()
                    };
                return n.i18nFormat(b, c, a).replace(/<(\w+)[^>]*?>\s*<\/\1>/g, "")
            },
            getLinkedDescription: function () {
                var a =
                    this.linkedDescriptionElement;
                return z(a && a.innerHTML || "")
            },
            getLongdescText: function () {
                var a = this.chart.options,
                    b = a.caption;
                b = b && b.text;
                var c = this.getLinkedDescription();
                return a.accessibility.description || c || b || ""
            },
            getTypeDescriptionText: function () {
                var a = this.chart;
                return a.types ? a.options.accessibility.typeDescription || a.getTypeDescription(a.types) : ""
            },
            getDataTableButtonText: function (a) {
                var b = this.chart;
                b = b.langFormat("accessibility.table.viewAsDataTableButtonText", {
                    chart: b,
                    chartTitle: w(b)
                });
                return '<button id="' +
                    a + '">' + b + "</button>"
            },
            getSonifyButtonText: function (a) {
                var b = this.chart;
                if (b.options.sonification && !1 === b.options.sonification.enabled) return "";
                b = b.langFormat("accessibility.sonification.playAsSoundButtonText", {
                    chart: b,
                    chartTitle: w(b)
                });
                return '<button id="' + a + '">' + b + "</button>"
            },
            getSubtitleText: function () {
                var a = this.chart.options.subtitle;
                return z(a && a.text || "")
            },
            getEndOfChartMarkerText: function () {
                var a = this.chart,
                    b = a.langFormat("accessibility.screenReaderSection.endOfChartMarker", {
                        chart: a
                    });
                return '<div id="highcharts-end-of-chart-marker-' +
                    a.index + '">' + b + "</div>"
            },
            onDataTableCreated: function (a) {
                var b = this.chart;
                if (b.options.accessibility.enabled) {
                    this.viewDataTableButton && this.viewDataTableButton.setAttribute("aria-expanded", "true");
                    var c = a.tree.attributes || {};
                    c.tabindex = -1;
                    c.summary = b.langFormat("accessibility.table.tableSummary", {
                        chart: b
                    });
                    a.tree.attributes = c
                }
            },
            focusDataTable: function () {
                var a = this.dataTableDiv;
                (a = a && a.getElementsByTagName("table")[0]) && a.focus && a.focus()
            },
            initSonifyButton: function (a) {
                var b = this,
                    c = this.sonifyButton =
                    N(a),
                    d = this.chart,
                    e = function (a) {
                        c && (c.setAttribute("aria-hidden", "true"), c.setAttribute("aria-label", ""));
                        a.preventDefault();
                        a.stopPropagation();
                        a = d.langFormat("accessibility.sonification.playAsSoundClickAnnouncement", {
                            chart: d
                        });
                        b.announcer.announce(a);
                        setTimeout(function () {
                            c && (c.removeAttribute("aria-hidden"), c.removeAttribute("aria-label"));
                            d.sonify && d.sonify()
                        }, 1E3)
                    };
                c && d && (D(c, {
                    tabindex: -1
                }), c.onclick = function (a) {
                    (d.options.accessibility && d.options.accessibility.screenReaderSection.onPlayAsSoundClick ||
                        e).call(this, a, d)
                })
            },
            initDataTableButton: function (a) {
                var b = this.viewDataTableButton = N(a),
                    c = this.chart;
                a = a.replace("hc-linkto-", "");
                b && (D(b, {
                    tabindex: -1,
                    "aria-expanded": !!N(a)
                }), b.onclick = c.options.accessibility.screenReaderSection.onViewDataTableClick || function () {
                    c.viewData()
                })
            },
            getAxesDescription: function () {
                var b = this.chart,
                    c = function (c, d) {
                        c = b[c];
                        return 1 < c.length || c[0] && a(c[0].options.accessibility && c[0].options.accessibility.enabled, d)
                    },
                    d = !!b.types && 0 > b.types.indexOf("map"),
                    e = !!b.hasCartesianSeries,
                    f = c("xAxis", !b.angular && e && d);
                c = c("yAxis", e && d);
                d = {};
                f && (d.xAxis = this.getAxisDescriptionText("xAxis"));
                c && (d.yAxis = this.getAxisDescriptionText("yAxis"));
                return d
            },
            getAxisDescriptionText: function (a) {
                var b = this.chart,
                    c = b[a];
                return b.langFormat("accessibility.axis." + a + "Description" + (1 < c.length ? "Plural" : "Singular"), {
                    chart: b,
                    names: c.map(function (a) {
                        return f(a)
                    }),
                    ranges: c.map(function (a) {
                        return y(a)
                    }),
                    numAxes: c.length
                })
            },
            destroy: function () {
                this.announcer && this.announcer.destroy()
            }
        });
        return e
    });
    v(b, "Accessibility/Components/ContainerComponent.js",
        [b["Accessibility/AccessibilityComponent.js"], b["Accessibility/KeyboardNavigationHandler.js"], b["Accessibility/Utils/ChartUtilities.js"], b["Core/Globals.js"], b["Accessibility/Utils/HTMLUtilities.js"], b["Core/Utilities.js"]],
        function (b, e, q, n, r, v) {
            var m = q.unhideChartElementFromAT,
                l = q.getChartTitle,
                k = n.doc,
                p = r.stripHTMLTagsFromString;
            q = v.extend;
            n = function () {};
            n.prototype = new b;
            q(n.prototype, {
                onChartUpdate: function () {
                    this.handleSVGTitleElement();
                    this.setSVGContainerLabel();
                    this.setGraphicContainerAttrs();
                    this.setRenderToAttrs();
                    this.makeCreditsAccessible()
                },
                handleSVGTitleElement: function () {
                    var b = this.chart,
                        c = "highcharts-title-" + b.index,
                        a = p(b.langFormat("accessibility.svgContainerTitle", {
                            chartTitle: l(b)
                        }));
                    if (a.length) {
                        var d = this.svgTitleElement = this.svgTitleElement || k.createElementNS("http://www.w3.org/2000/svg", "title");
                        d.textContent = a;
                        d.id = c;
                        b.renderTo.insertBefore(d, b.renderTo.firstChild)
                    }
                },
                setSVGContainerLabel: function () {
                    var b = this.chart,
                        c = b.langFormat("accessibility.svgContainerLabel", {
                            chartTitle: l(b)
                        });
                    b.renderer.box && c.length && b.renderer.box.setAttribute("aria-label", c)
                },
                setGraphicContainerAttrs: function () {
                    var b = this.chart,
                        c = b.langFormat("accessibility.graphicContainerLabel", {
                            chartTitle: l(b)
                        });
                    c.length && b.container.setAttribute("aria-label", c)
                },
                setRenderToAttrs: function () {
                    var b = this.chart;
                    "disabled" !== b.options.accessibility.landmarkVerbosity ? b.renderTo.setAttribute("role", "region") : b.renderTo.removeAttribute("role");
                    b.renderTo.setAttribute("aria-label", b.langFormat("accessibility.chartContainerLabel", {
                        title: l(b),
                        chart: b
                    }))
                },
                makeCreditsAccessible: function () {
                    var b = this.chart,
                        c = b.credits;
                    c && (c.textStr && c.element.setAttribute("aria-label", b.langFormat("accessibility.credits", {
                        creditsStr: p(c.textStr)
                    })), m(b, c.element))
                },
                getKeyboardNavigation: function () {
                    var b = this.chart;
                    return new e(b, {
                        keyCodeMap: [],
                        validate: function () {
                            return !0
                        },
                        init: function () {
                            var c = b.accessibility;
                            c && c.keyboardNavigation.tabindexContainer.focus()
                        }
                    })
                },
                destroy: function () {
                    this.chart.renderTo.setAttribute("aria-hidden", !0)
                }
            });
            return n
        });
    v(b, "Accessibility/HighContrastMode.js", [b["Core/Globals.js"]], function (b) {
        var e = b.doc,
            q = b.isMS,
            n = b.win;
        return {
            isHighContrastModeActive: function () {
                var b = /(Edg)/.test(n.navigator.userAgent);
                if (n.matchMedia && b) return n.matchMedia("(-ms-high-contrast: active)").matches;
                if (q && n.getComputedStyle) {
                    b = e.createElement("div");
                    b.style.backgroundImage = "url(data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==)";
                    e.body.appendChild(b);
                    var w = (b.currentStyle || n.getComputedStyle(b)).backgroundImage;
                    e.body.removeChild(b);
                    return "none" === w
                }
                return !1
            },
            setHighContrastTheme: function (b) {
                b.highContrastModeActive = !0;
                var e = b.options.accessibility.highContrastTheme;
                b.update(e, !1);
                b.series.forEach(function (b) {
                    var l = e.plotOptions[b.type] || {};
                    b.update({
                        color: l.color || "windowText",
                        colors: [l.color || "windowText"],
                        borderColor: l.borderColor || "window"
                    });
                    b.points.forEach(function (b) {
                        b.options && b.options.color && b.update({
                            color: l.color || "windowText",
                            borderColor: l.borderColor || "window"
                        }, !1)
                    })
                });
                b.redraw()
            }
        }
    });
    v(b, "Accessibility/HighContrastTheme.js",
        [],
        function () {
            return {
                chart: {
                    backgroundColor: "window"
                },
                title: {
                    style: {
                        color: "windowText"
                    }
                },
                subtitle: {
                    style: {
                        color: "windowText"
                    }
                },
                colorAxis: {
                    minColor: "windowText",
                    maxColor: "windowText",
                    stops: []
                },
                colors: ["windowText"],
                xAxis: {
                    gridLineColor: "windowText",
                    labels: {
                        style: {
                            color: "windowText"
                        }
                    },
                    lineColor: "windowText",
                    minorGridLineColor: "windowText",
                    tickColor: "windowText",
                    title: {
                        style: {
                            color: "windowText"
                        }
                    }
                },
                yAxis: {
                    gridLineColor: "windowText",
                    labels: {
                        style: {
                            color: "windowText"
                        }
                    },
                    lineColor: "windowText",
                    minorGridLineColor: "windowText",
                    tickColor: "windowText",
                    title: {
                        style: {
                            color: "windowText"
                        }
                    }
                },
                tooltip: {
                    backgroundColor: "window",
                    borderColor: "windowText",
                    style: {
                        color: "windowText"
                    }
                },
                plotOptions: {
                    series: {
                        lineColor: "windowText",
                        fillColor: "window",
                        borderColor: "windowText",
                        edgeColor: "windowText",
                        borderWidth: 1,
                        dataLabels: {
                            connectorColor: "windowText",
                            color: "windowText",
                            style: {
                                color: "windowText",
                                textOutline: "none"
                            }
                        },
                        marker: {
                            lineColor: "windowText",
                            fillColor: "windowText"
                        }
                    },
                    pie: {
                        color: "window",
                        colors: ["window"],
                        borderColor: "windowText",
                        borderWidth: 1
                    },
                    boxplot: {
                        fillColor: "window"
                    },
                    candlestick: {
                        lineColor: "windowText",
                        fillColor: "window"
                    },
                    errorbar: {
                        fillColor: "window"
                    }
                },
                legend: {
                    backgroundColor: "window",
                    itemStyle: {
                        color: "windowText"
                    },
                    itemHoverStyle: {
                        color: "windowText"
                    },
                    itemHiddenStyle: {
                        color: "#555"
                    },
                    title: {
                        style: {
                            color: "windowText"
                        }
                    }
                },
                credits: {
                    style: {
                        color: "windowText"
                    }
                },
                labels: {
                    style: {
                        color: "windowText"
                    }
                },
                drilldown: {
                    activeAxisLabelStyle: {
                        color: "windowText"
                    },
                    activeDataLabelStyle: {
                        color: "windowText"
                    }
                },
                navigation: {
                    buttonOptions: {
                        symbolStroke: "windowText",
                        theme: {
                            fill: "window"
                        }
                    }
                },
                rangeSelector: {
                    buttonTheme: {
                        fill: "window",
                        stroke: "windowText",
                        style: {
                            color: "windowText"
                        },
                        states: {
                            hover: {
                                fill: "window",
                                stroke: "windowText",
                                style: {
                                    color: "windowText"
                                }
                            },
                            select: {
                                fill: "#444",
                                stroke: "windowText",
                                style: {
                                    color: "windowText"
                                }
                            }
                        }
                    },
                    inputBoxBorderColor: "windowText",
                    inputStyle: {
                        backgroundColor: "window",
                        color: "windowText"
                    },
                    labelStyle: {
                        color: "windowText"
                    }
                },
                navigator: {
                    handles: {
                        backgroundColor: "window",
                        borderColor: "windowText"
                    },
                    outlineColor: "windowText",
                    maskFill: "transparent",
                    series: {
                        color: "windowText",
                        lineColor: "windowText"
                    },
                    xAxis: {
                        gridLineColor: "windowText"
                    }
                },
                scrollbar: {
                    barBackgroundColor: "#444",
                    barBorderColor: "windowText",
                    buttonArrowColor: "windowText",
                    buttonBackgroundColor: "window",
                    buttonBorderColor: "windowText",
                    rifleColor: "windowText",
                    trackBackgroundColor: "window",
                    trackBorderColor: "windowText"
                }
            }
        });
    v(b, "Accessibility/Options/Options.js", [b["Core/Color/Palette.js"]], function (b) {
        return {
            accessibility: {
                enabled: !0,
                screenReaderSection: {
                    beforeChartFormat: "<{headingTagName}>{chartTitle}</{headingTagName}><div>{typeDescription}</div><div>{chartSubtitle}</div><div>{chartLongdesc}</div><div>{playAsSoundButton}</div><div>{viewTableButton}</div><div>{xAxisDescription}</div><div>{yAxisDescription}</div><div>{annotationsTitle}{annotationsList}</div>",
                    afterChartFormat: "{endOfChartMarker}",
                    axisRangeDateFormat: "%Y-%m-%d %H:%M:%S"
                },
                series: {
                    describeSingleSeries: !1,
                    pointDescriptionEnabledThreshold: 200
                },
                point: {
                    valueDescriptionFormat: "{index}. {xDescription}{separator}{value}."
                },
                landmarkVerbosity: "all",
                linkedDescription: '*[data-highcharts-chart="{index}"] + .highcharts-description',
                keyboardNavigation: {
                    enabled: !0,
                    focusBorder: {
                        enabled: !0,
                        hideBrowserFocusOutline: !0,
                        style: {
                            color: b.highlightColor80,
                            lineWidth: 2,
                            borderRadius: 3
                        },
                        margin: 2
                    },
                    order: ["series", "zoom",
                        "rangeSelector", "legend", "chartMenu"
                    ],
                    wrapAround: !0,
                    seriesNavigation: {
                        skipNullPoints: !0,
                        pointNavigationEnabledThreshold: !1
                    }
                },
                announceNewData: {
                    enabled: !1,
                    minAnnounceInterval: 5E3,
                    interruptUser: !1
                }
            },
            legend: {
                accessibility: {
                    enabled: !0,
                    keyboardNavigation: {
                        enabled: !0
                    }
                }
            },
            exporting: {
                accessibility: {
                    enabled: !0
                }
            }
        }
    });
    v(b, "Accessibility/Options/LangOptions.js", [], function () {
        return {
            accessibility: {
                defaultChartTitle: "Chart",
                chartContainerLabel: "{title}. Highcharts interactive chart.",
                svgContainerLabel: "Interactive chart",
                drillUpButton: "{buttonText}",
                credits: "Chart credits: {creditsStr}",
                thousandsSep: ",",
                svgContainerTitle: "",
                graphicContainerLabel: "",
                screenReaderSection: {
                    beforeRegionLabel: "Chart screen reader information, {chartTitle}.",
                    afterRegionLabel: "",
                    annotations: {
                        heading: "Chart annotations summary",
                        descriptionSinglePoint: "{annotationText}. Related to {annotationPoint}",
                        descriptionMultiplePoints: "{annotationText}. Related to {annotationPoint}{ Also related to, #each(additionalAnnotationPoints)}",
                        descriptionNoPoints: "{annotationText}"
                    },
                    endOfChartMarker: "End of interactive chart."
                },
                sonification: {
                    playAsSoundButtonText: "Play as sound, {chartTitle}",
                    playAsSoundClickAnnouncement: "Play"
                },
                legend: {
                    legendLabelNoTitle: "Toggle series visibility, {chartTitle}",
                    legendLabel: "Chart legend: {legendTitle}",
                    legendItem: "Show {itemName}"
                },
                zoom: {
                    mapZoomIn: "Zoom chart",
                    mapZoomOut: "Zoom out chart",
                    resetZoomButton: "Reset zoom"
                },
                rangeSelector: {
                    dropdownLabel: "{rangeTitle}",
                    minInputLabel: "Select start date.",
                    maxInputLabel: "Select end date.",
                    clickButtonAnnouncement: "Viewing {axisRangeDescription}"
                },
                table: {
                    viewAsDataTableButtonText: "View as data table, {chartTitle}",
                    tableSummary: "Table representation of chart."
                },
                announceNewData: {
                    newDataAnnounce: "Updated data for chart {chartTitle}",
                    newSeriesAnnounceSingle: "New data series: {seriesDesc}",
                    newPointAnnounceSingle: "New data point: {pointDesc}",
                    newSeriesAnnounceMultiple: "New data series in chart {chartTitle}: {seriesDesc}",
                    newPointAnnounceMultiple: "New data point in chart {chartTitle}: {pointDesc}"
                },
                seriesTypeDescriptions: {
                    boxplot: "Box plot charts are typically used to display groups of statistical data. Each data point in the chart can have up to 5 values: minimum, lower quartile, median, upper quartile, and maximum.",
                    arearange: "Arearange charts are line charts displaying a range between a lower and higher value for each point.",
                    areasplinerange: "These charts are line charts displaying a range between a lower and higher value for each point.",
                    bubble: "Bubble charts are scatter charts where each data point also has a size value.",
                    columnrange: "Columnrange charts are column charts displaying a range between a lower and higher value for each point.",
                    errorbar: "Errorbar series are used to display the variability of the data.",
                    funnel: "Funnel charts are used to display reduction of data in stages.",
                    pyramid: "Pyramid charts consist of a single pyramid with item heights corresponding to each point value.",
                    waterfall: "A waterfall chart is a column chart where each column contributes towards a total end value."
                },
                chartTypes: {
                    emptyChart: "Empty chart",
                    mapTypeDescription: "Map of {mapTitle} with {numSeries} data series.",
                    unknownMap: "Map of unspecified region with {numSeries} data series.",
                    combinationChart: "Combination chart with {numSeries} data series.",
                    defaultSingle: "Chart with {numPoints} data {#plural(numPoints, points, point)}.",
                    defaultMultiple: "Chart with {numSeries} data series.",
                    splineSingle: "Line chart with {numPoints} data {#plural(numPoints, points, point)}.",
                    splineMultiple: "Line chart with {numSeries} lines.",
                    lineSingle: "Line chart with {numPoints} data {#plural(numPoints, points, point)}.",
                    lineMultiple: "Line chart with {numSeries} lines.",
                    columnSingle: "Bar chart with {numPoints} {#plural(numPoints, bars, bar)}.",
                    columnMultiple: "Bar chart with {numSeries} data series.",
                    barSingle: "Bar chart with {numPoints} {#plural(numPoints, bars, bar)}.",
                    barMultiple: "Bar chart with {numSeries} data series.",
                    pieSingle: "Pie chart with {numPoints} {#plural(numPoints, slices, slice)}.",
                    pieMultiple: "Pie chart with {numSeries} pies.",
                    scatterSingle: "Scatter chart with {numPoints} {#plural(numPoints, points, point)}.",
                    scatterMultiple: "Scatter chart with {numSeries} data series.",
                    boxplotSingle: "Boxplot with {numPoints} {#plural(numPoints, boxes, box)}.",
                    boxplotMultiple: "Boxplot with {numSeries} data series.",
                    bubbleSingle: "Bubble chart with {numPoints} {#plural(numPoints, bubbles, bubble)}.",
                    bubbleMultiple: "Bubble chart with {numSeries} data series."
                },
                axis: {
                    xAxisDescriptionSingular: "The chart has 1 X axis displaying {names[0]}. {ranges[0]}",
                    xAxisDescriptionPlural: "The chart has {numAxes} X axes displaying {#each(names, -1) }and {names[-1]}.",
                    yAxisDescriptionSingular: "The chart has 1 Y axis displaying {names[0]}. {ranges[0]}",
                    yAxisDescriptionPlural: "The chart has {numAxes} Y axes displaying {#each(names, -1) }and {names[-1]}.",
                    timeRangeDays: "Range: {range} days.",
                    timeRangeHours: "Range: {range} hours.",
                    timeRangeMinutes: "Range: {range} minutes.",
                    timeRangeSeconds: "Range: {range} seconds.",
                    rangeFromTo: "Range: {rangeFrom} to {rangeTo}.",
                    rangeCategories: "Range: {numCategories} categories."
                },
                exporting: {
                    chartMenuLabel: "Chart menu",
                    menuButtonLabel: "View chart menu",
                    exportRegionLabel: "Chart menu, {chartTitle}"
                },
                series: {
                    summary: {
                        "default": "{name}, series {ix} of {numSeries} with {numPoints} data {#plural(numPoints, points, point)}.",
                        defaultCombination: "{name}, series {ix} of {numSeries} with {numPoints} data {#plural(numPoints, points, point)}.",
                        line: "{name}, line {ix} of {numSeries} with {numPoints} data {#plural(numPoints, points, point)}.",
                        lineCombination: "{name}, series {ix} of {numSeries}. Line with {numPoints} data {#plural(numPoints, points, point)}.",
                        spline: "{name}, line {ix} of {numSeries} with {numPoints} data {#plural(numPoints, points, point)}.",
                        splineCombination: "{name}, series {ix} of {numSeries}. Line with {numPoints} data {#plural(numPoints, points, point)}.",
                        column: "{name}, bar series {ix} of {numSeries} with {numPoints} {#plural(numPoints, bars, bar)}.",
                        columnCombination: "{name}, series {ix} of {numSeries}. Bar series with {numPoints} {#plural(numPoints, bars, bar)}.",
                        bar: "{name}, bar series {ix} of {numSeries} with {numPoints} {#plural(numPoints, bars, bar)}.",
                        barCombination: "{name}, series {ix} of {numSeries}. Bar series with {numPoints} {#plural(numPoints, bars, bar)}.",
                        pie: "{name}, pie {ix} of {numSeries} with {numPoints} {#plural(numPoints, slices, slice)}.",
                        pieCombination: "{name}, series {ix} of {numSeries}. Pie with {numPoints} {#plural(numPoints, slices, slice)}.",
                        scatter: "{name}, scatter plot {ix} of {numSeries} with {numPoints} {#plural(numPoints, points, point)}.",
                        scatterCombination: "{name}, series {ix} of {numSeries}, scatter plot with {numPoints} {#plural(numPoints, points, point)}.",
                        boxplot: "{name}, boxplot {ix} of {numSeries} with {numPoints} {#plural(numPoints, boxes, box)}.",
                        boxplotCombination: "{name}, series {ix} of {numSeries}. Boxplot with {numPoints} {#plural(numPoints, boxes, box)}.",
                        bubble: "{name}, bubble series {ix} of {numSeries} with {numPoints} {#plural(numPoints, bubbles, bubble)}.",
                        bubbleCombination: "{name}, series {ix} of {numSeries}. Bubble series with {numPoints} {#plural(numPoints, bubbles, bubble)}.",
                        map: "{name}, map {ix} of {numSeries} with {numPoints} {#plural(numPoints, areas, area)}.",
                        mapCombination: "{name}, series {ix} of {numSeries}. Map with {numPoints} {#plural(numPoints, areas, area)}.",
                        mapline: "{name}, line {ix} of {numSeries} with {numPoints} data {#plural(numPoints, points, point)}.",
                        maplineCombination: "{name}, series {ix} of {numSeries}. Line with {numPoints} data {#plural(numPoints, points, point)}.",
                        mapbubble: "{name}, bubble series {ix} of {numSeries} with {numPoints} {#plural(numPoints, bubbles, bubble)}.",
                        mapbubbleCombination: "{name}, series {ix} of {numSeries}. Bubble series with {numPoints} {#plural(numPoints, bubbles, bubble)}."
                    },
                    description: "{description}",
                    xAxisDescription: "X axis, {name}",
                    yAxisDescription: "Y axis, {name}",
                    nullPointValue: "No value",
                    pointAnnotationsDescription: "{Annotation: #each(annotations). }"
                }
            }
        }
    });
    v(b, "Accessibility/Options/DeprecatedOptions.js", [b["Core/Utilities.js"]], function (b) {
        function e(b, e, g) {
            for (var c, a = 0; a < e.length - 1; ++a) c = e[a], b = b[c] = l(b[c], {});
            b[e[e.length - 1]] = g
        }

        function q(b, l, g, c) {
            function a(a, b) {
                return b.reduce(function (a, b) {
                    return a[b]
                }, a)
            }
            var d = a(b.options, l),
                f = a(b.options, g);
            Object.keys(c).forEach(function (a) {
                var k, p = d[a];
                "undefined" !== typeof p && (e(f, c[a], p), m(32, !1, b, (k = {}, k[l.join(".") + "." + a] = g.join(".") + "." + c[a].join("."), k)))
            })
        }

        function n(b) {
            var e = b.options.chart,
                g = b.options.accessibility || {};
            ["description", "typeDescription"].forEach(function (c) {
                var a;
                e[c] && (g[c] = e[c], m(32, !1, b, (a = {}, a["chart." + c] = "use accessibility." + c, a)))
            })
        }

        function r(b) {
            b.axes.forEach(function (e) {
                (e = e.options) && e.description && (e.accessibility = e.accessibility || {}, e.accessibility.description = e.description, m(32, !1, b, {
                    "axis.description": "use axis.accessibility.description"
                }))
            })
        }

        function w(b) {
            var k = {
                description: ["accessibility", "description"],
                exposeElementToA11y: ["accessibility", "exposeAsGroupOnly"],
                pointDescriptionFormatter: ["accessibility",
                    "pointDescriptionFormatter"
                ],
                skipKeyboardNavigation: ["accessibility", "keyboardNavigation", "enabled"]
            };
            b.series.forEach(function (g) {
                Object.keys(k).forEach(function (c) {
                    var a, d = g.options[c];
                    "undefined" !== typeof d && (e(g.options, k[c], "skipKeyboardNavigation" === c ? !d : d), m(32, !1, b, (a = {}, a["series." + c] = "series." + k[c].join("."), a)))
                })
            })
        }
        var m = b.error,
            l = b.pick;
        return function (b) {
            n(b);
            r(b);
            b.series && w(b);
            q(b, ["accessibility"], ["accessibility"], {
                pointDateFormat: ["point", "dateFormat"],
                pointDateFormatter: ["point",
                    "dateFormatter"
                ],
                pointDescriptionFormatter: ["point", "descriptionFormatter"],
                pointDescriptionThreshold: ["series", "pointDescriptionEnabledThreshold"],
                pointNavigationThreshold: ["keyboardNavigation", "seriesNavigation", "pointNavigationEnabledThreshold"],
                pointValueDecimals: ["point", "valueDecimals"],
                pointValuePrefix: ["point", "valuePrefix"],
                pointValueSuffix: ["point", "valueSuffix"],
                screenReaderSectionFormatter: ["screenReaderSection", "beforeChartFormatter"],
                describeSingleSeries: ["series", "describeSingleSeries"],
                seriesDescriptionFormatter: ["series", "descriptionFormatter"],
                onTableAnchorClick: ["screenReaderSection", "onViewDataTableClick"],
                axisRangeDateFormat: ["screenReaderSection", "axisRangeDateFormat"]
            });
            q(b, ["accessibility", "keyboardNavigation"], ["accessibility", "keyboardNavigation", "seriesNavigation"], {
                skipNullPoints: ["skipNullPoints"],
                mode: ["mode"]
            });
            q(b, ["lang", "accessibility"], ["lang", "accessibility"], {
                legendItem: ["legend", "legendItem"],
                legendLabel: ["legend", "legendLabel"],
                mapZoomIn: ["zoom", "mapZoomIn"],
                mapZoomOut: ["zoom", "mapZoomOut"],
                resetZoomButton: ["zoom", "resetZoomButton"],
                screenReaderRegionLabel: ["screenReaderSection", "beforeRegionLabel"],
                rangeSelectorButton: ["rangeSelector", "buttonText"],
                rangeSelectorMaxInput: ["rangeSelector", "maxInputLabel"],
                rangeSelectorMinInput: ["rangeSelector", "minInputLabel"],
                svgContainerEnd: ["screenReaderSection", "endOfChartMarker"],
                viewAsDataTable: ["table", "viewAsDataTableButtonText"],
                tableSummary: ["table", "tableSummary"]
            })
        }
    });
    v(b, "Accessibility/A11yI18n.js", [b["Core/Chart/Chart.js"],
        b["Core/Globals.js"], b["Core/FormatUtilities.js"], b["Core/Utilities.js"]
    ], function (b, e, q, n) {
        function r(b, e) {
            var k = b.indexOf("#each("),
                g = b.indexOf("#plural("),
                c = b.indexOf("["),
                a = b.indexOf("]");
            if (-1 < k) {
                a = b.slice(k).indexOf(")") + k;
                g = b.substring(0, k);
                c = b.substring(a + 1);
                a = b.substring(k + 6, a).split(",");
                k = Number(a[1]);
                b = "";
                if (e = e[a[0]])
                    for (k = isNaN(k) ? e.length : k, k = 0 > k ? e.length + k : Math.min(k, e.length), a = 0; a < k; ++a) b += g + e[a] + c;
                return b.length ? b : ""
            }
            if (-1 < g) {
                c = b.slice(g).indexOf(")") + g;
                g = b.substring(g + 8, c).split(",");
                switch (Number(e[g[0]])) {
                    case 0:
                        b = m(g[4], g[1]);
                        break;
                    case 1:
                        b = m(g[2], g[1]);
                        break;
                    case 2:
                        b = m(g[3], g[1]);
                        break;
                    default:
                        b = g[1]
                }
                b ? (e = b, e = e.trim && e.trim() || e.replace(/^\s+|\s+$/g, "")) : e = "";
                return e
            }
            return -1 < c ? (g = b.substring(0, c), c = Number(b.substring(c + 1, a)), b = void 0, e = e[g], !isNaN(c) && e && (0 > c ? (b = e[e.length + c], "undefined" === typeof b && (b = e[0])) : (b = e[c], "undefined" === typeof b && (b = e[e.length - 1]))), "undefined" !== typeof b ? b : "") : "{" + b + "}"
        }
        var w = q.format,
            m = n.pick;
        e.i18nFormat = function (b, e, m) {
            var g = function (a, b) {
                    a =
                        a.slice(b || 0);
                    var c = a.indexOf("{"),
                        d = a.indexOf("}");
                    if (-1 < c && d > c) return {
                        statement: a.substring(c + 1, d),
                        begin: b + c + 1,
                        end: b + d
                    }
                },
                c = [],
                a = 0;
            do {
                var d = g(b, a);
                var f = b.substring(a, d && d.begin - 1);
                f.length && c.push({
                    value: f,
                    type: "constant"
                });
                d && c.push({
                    value: d.statement,
                    type: "statement"
                });
                a = d ? d.end + 1 : a + 1
            } while (d);
            c.forEach(function (a) {
                "statement" === a.type && (a.value = r(a.value, e))
            });
            return w(c.reduce(function (a, b) {
                return a + b.value
            }, ""), e, m)
        };
        b.prototype.langFormat = function (b, k) {
            b = b.split(".");
            for (var l = this.options.lang,
                    g = 0; g < b.length; ++g) l = l && l[b[g]];
            return "string" === typeof l ? e.i18nFormat(l, k, this) : ""
        }
    });
    v(b, "Accessibility/FocusBorder.js", [b["Core/Chart/Chart.js"], b["Core/Renderer/SVG/SVGElement.js"], b["Core/Renderer/SVG/SVGLabel.js"], b["Core/Utilities.js"]], function (b, e, q, n) {
        function r(b) {
            if (!b.focusBorderDestroyHook) {
                var a = b.destroy;
                b.destroy = function () {
                    b.focusBorder && b.focusBorder.destroy && b.focusBorder.destroy();
                    return a.apply(b, arguments)
                };
                b.focusBorderDestroyHook = a
            }
        }

        function w(b) {
            for (var a = [], c = 1; c < arguments.length; c++) a[c -
                1] = arguments[c];
            b.focusBorderUpdateHooks || (b.focusBorderUpdateHooks = {}, g.forEach(function (c) {
                c += "Setter";
                var d = b[c] || b._defaultSetter;
                b.focusBorderUpdateHooks[c] = d;
                b[c] = function () {
                    var c = d.apply(b, arguments);
                    b.addFocusBorder.apply(b, a);
                    return c
                }
            }))
        }

        function m(b) {
            b.focusBorderUpdateHooks && (Object.keys(b.focusBorderUpdateHooks).forEach(function (a) {
                var c = b.focusBorderUpdateHooks[a];
                c === b._defaultSetter ? delete b[a] : b[a] = c
            }), delete b.focusBorderUpdateHooks)
        }
        var l = n.addEvent,
            k = n.extend,
            p = n.pick,
            g = "x y transform width height r d stroke-width".split(" ");
        k(e.prototype, {
            addFocusBorder: function (b, a) {
                this.focusBorder && this.removeFocusBorder();
                var c = this.getBBox(),
                    e = p(b, 3);
                c.x += this.translateX ? this.translateX : 0;
                c.y += this.translateY ? this.translateY : 0;
                var g = c.x - e,
                    k = c.y - e,
                    l = c.width + 2 * e,
                    m = c.height + 2 * e,
                    n = this instanceof q;
                if ("text" === this.element.nodeName || n) {
                    var v = !!this.rotation;
                    if (n) var x = {
                        x: v ? 1 : 0,
                        y: 0
                    };
                    else {
                        var z = x = 0;
                        "middle" === this.attr("text-anchor") ? x = z = .5 : this.rotation ? x = .25 : z = .75;
                        x = {
                            x: x,
                            y: z
                        }
                    }
                    z = +this.attr("x");
                    var E = +this.attr("y");
                    isNaN(z) || (g = z - c.width *
                        x.x - e);
                    isNaN(E) || (k = E - c.height * x.y - e);
                    n && v && (n = l, l = m, m = n, isNaN(z) || (g = z - c.height * x.x - e), isNaN(E) || (k = E - c.width * x.y - e))
                }
                this.focusBorder = this.renderer.rect(g, k, l, m, parseInt((a && a.r || 0).toString(), 10)).addClass("highcharts-focus-border").attr({
                    zIndex: 99
                }).add(this.parentGroup);
                this.renderer.styledMode || this.focusBorder.attr({
                    stroke: a && a.stroke,
                    "stroke-width": a && a.strokeWidth
                });
                w(this, b, a);
                r(this)
            },
            removeFocusBorder: function () {
                m(this);
                this.focusBorderDestroyHook && (this.destroy = this.focusBorderDestroyHook,
                    delete this.focusBorderDestroyHook);
                this.focusBorder && (this.focusBorder.destroy(), delete this.focusBorder)
            }
        });
        b.prototype.renderFocusBorder = function () {
            var b = this.focusElement,
                a = this.options.accessibility.keyboardNavigation.focusBorder;
            b && (b.removeFocusBorder(), a.enabled && b.addFocusBorder(a.margin, {
                stroke: a.style.color,
                strokeWidth: a.style.lineWidth,
                r: a.style.borderRadius
            }))
        };
        b.prototype.setFocusToElement = function (b, a) {
            var c = this.options.accessibility.keyboardNavigation.focusBorder;
            (a = a || b.element) &&
            a.focus && (a.hcEvents && a.hcEvents.focusin || l(a, "focusin", function () {}), a.focus(), c.hideBrowserFocusOutline && (a.style.outline = "none"));
            this.focusElement && this.focusElement.removeFocusBorder();
            this.focusElement = b;
            this.renderFocusBorder()
        }
    });
    v(b, "Accessibility/Accessibility.js", [b["Core/Chart/Chart.js"], b["Accessibility/Utils/ChartUtilities.js"], b["Core/Globals.js"], b["Accessibility/KeyboardNavigationHandler.js"], b["Core/DefaultOptions.js"], b["Core/Series/Point.js"], b["Core/Series/Series.js"], b["Core/Utilities.js"],
        b["Accessibility/AccessibilityComponent.js"], b["Accessibility/KeyboardNavigation.js"], b["Accessibility/Components/LegendComponent.js"], b["Accessibility/Components/MenuComponent.js"], b["Accessibility/Components/SeriesComponent/SeriesComponent.js"], b["Accessibility/Components/ZoomComponent.js"], b["Accessibility/Components/RangeSelectorComponent.js"], b["Accessibility/Components/InfoRegionsComponent.js"], b["Accessibility/Components/ContainerComponent.js"], b["Accessibility/HighContrastMode.js"], b["Accessibility/HighContrastTheme.js"],
        b["Accessibility/Options/Options.js"], b["Accessibility/Options/LangOptions.js"], b["Accessibility/Options/DeprecatedOptions.js"], b["Accessibility/Utils/HTMLUtilities.js"]
    ], function (b, e, q, n, r, v, m, l, k, p, g, c, a, d, f, y, G, C, L, N, I, D, z) {
        function w(a) {
            this.init(a)
        }
        var t = q.doc,
            u = l.addEvent,
            x = l.extend,
            K = l.fireEvent,
            F = l.merge;
        F(!0, r.defaultOptions, N, {
            accessibility: {
                highContrastTheme: L
            },
            lang: I
        });
        q.A11yChartUtilities = e;
        q.A11yHTMLUtilities = z;
        q.KeyboardNavigationHandler = n;
        q.AccessibilityComponent = k;
        w.prototype = {
            init: function (a) {
                this.chart =
                    a;
                t.addEventListener && a.renderer.isSVG ? (D(a), this.initComponents(), this.keyboardNavigation = new p(a, this.components), this.update()) : a.renderTo.setAttribute("aria-hidden", !0)
            },
            initComponents: function () {
                var b = this.chart,
                    e = b.options.accessibility;
                this.components = {
                    container: new G,
                    infoRegions: new y,
                    legend: new g,
                    chartMenu: new c,
                    rangeSelector: new f,
                    series: new a,
                    zoom: new d
                };
                e.customComponents && x(this.components, e.customComponents);
                var k = this.components;
                this.getComponentOrder().forEach(function (a) {
                    k[a].initBase(b);
                    k[a].init()
                })
            },
            getComponentOrder: function () {
                if (!this.components) return [];
                if (!this.components.series) return Object.keys(this.components);
                var a = Object.keys(this.components).filter(function (a) {
                    return "series" !== a
                });
                return ["series"].concat(a)
            },
            update: function () {
                var a = this.components,
                    b = this.chart,
                    c = b.options.accessibility;
                K(b, "beforeA11yUpdate");
                b.types = this.getChartTypes();
                this.getComponentOrder().forEach(function (c) {
                    a[c].onChartUpdate();
                    K(b, "afterA11yComponentUpdate", {
                        name: c,
                        component: a[c]
                    })
                });
                this.keyboardNavigation.update(c.keyboardNavigation.order);
                !b.highContrastModeActive && C.isHighContrastModeActive() && C.setHighContrastTheme(b);
                K(b, "afterA11yUpdate", {
                    accessibility: this
                })
            },
            destroy: function () {
                var a = this.chart || {},
                    b = this.components;
                Object.keys(b).forEach(function (a) {
                    b[a].destroy();
                    b[a].destroyBase()
                });
                this.keyboardNavigation && this.keyboardNavigation.destroy();
                a.renderTo && a.renderTo.setAttribute("aria-hidden", !0);
                a.focusElement && a.focusElement.removeFocusBorder()
            },
            getChartTypes: function () {
                var a = {};
                this.chart.series.forEach(function (b) {
                    a[b.type] =
                        1
                });
                return Object.keys(a)
            }
        };
        b.prototype.updateA11yEnabled = function () {
            var a = this.accessibility,
                b = this.options.accessibility;
            b && b.enabled ? a ? a.update() : this.accessibility = new w(this) : a ? (a.destroy && a.destroy(), delete this.accessibility) : this.renderTo.setAttribute("aria-hidden", !0)
        };
        u(b, "render", function (a) {
            this.a11yDirty && this.renderTo && (delete this.a11yDirty, this.updateA11yEnabled());
            var b = this.accessibility;
            b && b.getComponentOrder().forEach(function (a) {
                b.components[a].onChartRender()
            })
        });
        u(b, "update",
            function (a) {
                if (a = a.options.accessibility) a.customComponents && (this.options.accessibility.customComponents = a.customComponents, delete a.customComponents), F(!0, this.options.accessibility, a), this.accessibility && this.accessibility.destroy && (this.accessibility.destroy(), delete this.accessibility);
                this.a11yDirty = !0
            });
        u(v, "update", function () {
            this.series.chart.accessibility && (this.series.chart.a11yDirty = !0)
        });
        ["addSeries", "init"].forEach(function (a) {
            u(b, a, function () {
                this.a11yDirty = !0
            })
        });
        ["update", "updatedData",
            "remove"
        ].forEach(function (a) {
            u(m, a, function () {
                this.chart.accessibility && (this.chart.a11yDirty = !0)
            })
        });
        ["afterDrilldown", "drillupall"].forEach(function (a) {
            u(b, a, function () {
                this.accessibility && this.accessibility.update()
            })
        });
        u(b, "destroy", function () {
            this.accessibility && this.accessibility.destroy()
        })
    });
    v(b, "masters/modules/accessibility.src.js", [], function () {})
});