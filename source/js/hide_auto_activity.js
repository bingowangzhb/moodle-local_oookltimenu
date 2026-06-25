/*
 * Hide [OOOK LTI AUTO] helper activities in course page and course index.
 *
 * This script is intentionally framework-free so it can run even when AMD
 * modules are deferred by themes/custom loaders.
 */
(function() {
    'use strict';

    if (window.localOookLtiMenuHideAutoLoaded) {
        return;
    }
    window.localOookLtiMenuHideAutoLoaded = true;

    var cfg = document.getElementById('local-oookltimenuauto-hidecfg');
    if (!cfg) {
        return;
    }

    var cmids = (cfg.getAttribute('data-cmids') || '')
        .split(',')
        .map(function(v) {
            return parseInt(v, 10);
        })
        .filter(function(v) {
            return Number.isFinite(v) && v > 0;
        });
    var marker = (cfg.getAttribute('data-marker') || '').trim();

    if (!cmids.length && !marker) {
        return;
    }

    var normalize = function(s) {
        return String(s || '').replace(/\s+/g, ' ').trim();
    };
    var markerNorm = normalize(marker);

    var hideNode = function(node) {
        if (!node) {
            return;
        }
        node.style.setProperty('display', 'none', 'important');
    };

    var hideActivity = function(node) {
        if (!node) {
            return;
        }
        hideNode(
            node.closest('li.activity') ||
            node.closest('.activity-item') ||
            node.closest('.activity') ||
            node.closest('.activitytitle') ||
            node.closest('[id^="module-"]') ||
            node
        );
    };

    var hideCourseIndex = function(node) {
        if (!node) {
            return;
        }
        hideNode(
            node.closest('li.courseindex-item') ||
            node.closest('.courseindex-item') ||
            node.closest('[id^="course-index-cm-"]') ||
            node
        );
    };

    var hideByCmid = function(cmid) {
        document.querySelectorAll(
            '#module-' + cmid +
            ',li.activity[data-id="' + cmid + '"]' +
            ',li.activity[data-for="cmitem"][data-id="' + cmid + '"]' +
            ',.inplaceeditable[data-itemtype="activityname"][data-itemid="' + cmid + '"]' +
            ',a[href*="/mod/lti/view.php?id=' + cmid + '"]' +
            ',a[href*="/mod/lti/view.php?id=' + cmid + '&"]'
        ).forEach(hideActivity);

        document.querySelectorAll(
            '#course-index-cm-' + cmid +
            ',li.courseindex-item[data-id="' + cmid + '"]' +
            ',li.courseindex-item[data-for="cm"][data-id="' + cmid + '"]' +
            ',a.courseindex-link[href*="id=' + cmid + '"]'
        ).forEach(hideCourseIndex);
    };

    var hideByMarker = function() {
        if (!markerNorm) {
            return;
        }

        document.querySelectorAll('.inplaceeditable[data-itemtype="activityname"]').forEach(function(node) {
            if (normalize(node.getAttribute('data-value')) === markerNorm) {
                hideActivity(node);
            }
        });

        document.querySelectorAll('.instancename').forEach(function(node) {
            if (normalize(node.textContent).indexOf(markerNorm) === 0) {
                hideActivity(node);
            }
        });

        document.querySelectorAll('a.courseindex-link').forEach(function(node) {
            if (normalize(node.textContent) === markerNorm) {
                hideCourseIndex(node);
            }
        });
    };

    var scheduled = false;
    var run = function() {
        cmids.forEach(hideByCmid);
        hideByMarker();
    };
    var scheduleRun = function() {
        if (scheduled) {
            return;
        }
        scheduled = true;
        window.requestAnimationFrame(function() {
            scheduled = false;
            run();
        });
    };

    run();
    var observer = new MutationObserver(scheduleRun);
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
})();

