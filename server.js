/**
 * Lightweight dev server that serves static files and
 * mirrors the PHP API endpoints from index.php.
 *
 * Usage: node server.js
 * Then open http://localhost:8000
 */

const http  = require('http');
const https = require('https');
const fs    = require('fs');
const path  = require('path');
const url   = require('url');

const PORT = 8000;
const ROOT = __dirname;

const MIME = {
    '.html': 'text/html',
    '.php':  'text/html',
    '.css':  'text/css',
    '.js':   'application/javascript',
    '.json': 'application/json',
    '.png':  'image/png',
    '.jpg':  'image/jpeg',
    '.svg':  'image/svg+xml',
    '.ico':  'image/x-icon',
};

// ─── API handlers (replicate index.php?action=...) ───

function apiHeadDemo() {
    return {
        method: 'HEAD',
        url: 'https://example.com/resource',
        status: 200,
        headers: [
            'HTTP/1.1 200 OK',
            'Content-Type: text/html; charset=UTF-8',
            'Content-Length: 3526',
            'Last-Modified: Thu, 28 Aug 2026 03:48:00 GMT',
            'ETag: "abc123def456"',
            'Cache-Control: max-age=3600',
            'Server: Apache/2.4.51',
            'X-Powered-By: PHP/8.2',
        ],
        body: null,
        body_size: 0,
        time_ms: 42 + Math.floor(Math.random() * 78),
    };
}

function apiGetDemo() {
    return {
        method: 'GET',
        url: 'https://example.com/resource',
        status: 200,
        headers: [
            'HTTP/1.1 200 OK',
            'Content-Type: text/html; charset=UTF-8',
            'Content-Length: 3526',
            'Last-Modified: Thu, 28 Aug 2026 03:48:00 GMT',
            'ETag: "abc123def456"',
            'Cache-Control: max-age=3600',
            'Server: Apache/2.4.51',
            'X-Powered-By: PHP/8.2',
        ],
        body: '<!DOCTYPE html><html><head><title>Example Page</title></head><body><h1>Hello World</h1><p>This is a full HTML body response with all its content...</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p><p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p></body></html>',
        body_size: 3526,
        time_ms: 180 + Math.floor(Math.random() * 220),
    };
}

function apiLiveHead(targetUrl) {
    return new Promise((resolve) => {
        if (!targetUrl) { resolve({ error: 'No URL provided' }); return; }
        try { new URL(targetUrl); } catch { resolve({ error: 'Invalid URL format' }); return; }

        const lib = targetUrl.startsWith('https') ? https : http;
        const start = Date.now();

        const req = lib.request(targetUrl, { method: 'HEAD', timeout: 10000, headers: { 'User-Agent': 'HTTP-HEAD-Presentation/1.0' } }, (res) => {
            const elapsed = Date.now() - start;
            const headerLines = [`HTTP/${res.httpVersion} ${res.statusCode} ${res.statusMessage}`];
            const raw = res.rawHeaders;
            for (let i = 0; i < raw.length; i += 2) {
                headerLines.push(`${raw[i]}: ${raw[i + 1]}`);
            }
            res.resume();
            resolve({ method: 'HEAD', url: targetUrl, status: res.statusCode, headers: headerLines, body: null, body_size: 0, time_ms: elapsed });
        });

        req.on('error', (err) => resolve({ error: err.message }));
        req.on('timeout', () => { req.destroy(); resolve({ error: 'Request timed out' }); });
        req.end();
    });
}

// ─── Server ───

const server = http.createServer(async (req, res) => {
    const parsed = url.parse(req.url, true);
    const pathname = parsed.pathname;
    const query = parsed.query;

    // API routes
    if (query.action) {
        res.setHeader('Content-Type', 'application/json');
        let data;
        switch (query.action) {
            case 'head_demo': data = apiHeadDemo(); break;
            case 'get_demo':  data = apiGetDemo();  break;
            case 'live_head': data = await apiLiveHead(query.url); break;
            default: data = { error: 'Unknown action' };
        }
        res.end(JSON.stringify(data));
        return;
    }

    // Static files
    let filePath = pathname === '/' ? '/index.html' : pathname;
    // Serve index.php as index.html (the PHP part is handled by our API above)
    if (filePath === '/index.html') filePath = '/index.php';

    const fullPath = path.join(ROOT, filePath);

    // Security: prevent directory traversal
    if (!fullPath.startsWith(ROOT)) {
        res.writeHead(403);
        res.end('Forbidden');
        return;
    }

    fs.readFile(fullPath, (err, data) => {
        if (err) {
            res.writeHead(404, { 'Content-Type': 'text/plain' });
            res.end('Not Found');
            return;
        }
        const ext = path.extname(fullPath).toLowerCase();
        let mime = MIME[ext] || 'application/octet-stream';

        // For .php, strip the PHP block and serve as HTML
        if (ext === '.php') {
            let html = data.toString('utf-8');
            // Remove the PHP API block: find the closing ?> tag and strip everything up to it
            const phpCloseIdx = html.lastIndexOf('?>');
            if (phpCloseIdx !== -1) {
                html = html.substring(phpCloseIdx + 2).trimStart();
            }
            data = Buffer.from(html, 'utf-8');
        }

        res.writeHead(200, { 'Content-Type': mime });
        res.end(data);
    });
});

server.listen(PORT, () => {
    console.log(`\n  🚀  HTTP HEAD Presentation Server`);
    console.log(`  ──────────────────────────────────`);
    console.log(`  Local:   http://localhost:${PORT}`);
    console.log(`  Press Ctrl+C to stop.\n`);
});
