(function () {
    'use strict';

    var months = {
        jan: '01', january: '01',
        feb: '02', february: '02',
        mar: '03', march: '03',
        apr: '04', april: '04',
        may: '05',
        jun: '06', june: '06',
        jul: '07', july: '07',
        aug: '08', august: '08',
        sep: '09', september: '09',
        oct: '10', october: '10',
        nov: '11', november: '11',
        dec: '12', december: '12'
    };

    function isValidDate(day, month, year) {
        var date = new Date(year, month - 1, day);
        return date.getFullYear() === year && date.getMonth() === month - 1 && date.getDate() === day;
    }

    function formatDateText(text) {
        text = text.replace(/\b(\d{4})-(\d{2})-(\d{2})(?:(T| )(\d{2}:\d{2}(?::\d{2})?))?\b/g, function (match, year, month, day, separator, time) {
            if (!isValidDate(Number(day), Number(month), Number(year))) {
                return match;
            }
            return day + '/' + month + '/' + year + (time ? ' ' + time : '');
        });

        text = text.replace(/\b(\d{1,2})-(\d{1,2})-(\d{4})\b/g, function (match, day, month, year) {
            day = Number(day);
            month = Number(month);
            year = Number(year);
            return isValidDate(day, month, year)
                ? String(day).padStart(2, '0') + '/' + String(month).padStart(2, '0') + '/' + year
                : match;
        });

        text = text.replace(/\b(?:(?:Mon|Tue|Wed|Thu|Fri|Sat|Sun)(?:day)?,?\s+)?(\d{1,2})\s+([A-Za-z]+)\s+(\d{4})\b/gi, function (match, day, monthName, year) {
            var month = months[monthName.toLowerCase()];
            day = Number(day);
            year = Number(year);
            return month && isValidDate(day, Number(month), year)
                ? String(day).padStart(2, '0') + '/' + month + '/' + year
                : match;
        });

        return text.replace(/\b([A-Za-z]+)\s+(\d{1,2}),?\s+(\d{4})\b/g, function (match, monthName, day, year) {
            var month = months[monthName.toLowerCase()];
            day = Number(day);
            year = Number(year);
            return month && isValidDate(day, Number(month), year)
                ? String(day).padStart(2, '0') + '/' + month + '/' + year
                : match;
        });
    }

    function formatTextNode(node) {
        var parent = node.parentElement;
        if (!parent || parent.closest('script,style,textarea,noscript,code,pre,[data-date-format-ignore]')) {
            return;
        }
        var formatted = formatDateText(node.nodeValue);
        if (formatted !== node.nodeValue) {
            node.nodeValue = formatted;
        }
    }

    function formatSubtree(root) {
        if (root.nodeType === Node.TEXT_NODE) {
            formatTextNode(root);
            return;
        }
        if (root.nodeType !== Node.ELEMENT_NODE && root.nodeType !== Node.DOCUMENT_NODE) {
            return;
        }
        if (root.nodeType === Node.ELEMENT_NODE && root.matches('script,style,textarea,noscript,code,pre,[data-date-format-ignore]')) {
            return;
        }
        var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT);
        var node;
        while ((node = walker.nextNode())) {
            formatTextNode(node);
        }
    }

    document.documentElement.lang = 'en-GB';
    formatSubtree(document.body);

    if (window.MutationObserver) {
        new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(formatSubtree);
            });
        }).observe(document.body, { childList: true, subtree: true });
    }
})();
