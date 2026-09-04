// Using the Fetch API
const response = await fetch('https://clickora.fwh.is/?i=1', {
  method: 'HEAD',
});

// Read metadata from headers (no body!)
const status     = response.status;
const type       = response.headers.get('content-type');
const size       = response.headers.get('content-length');
const lastMod    = response.headers.get('last-modified');
const etag       = response.headers.get('etag');

console.log(`Status: ${status}`);
console.log(`Size: ${size} bytes`);
console.log(`ETag: ${etag}`);

// response.body is null for HEAD
console.log(response.body); // null
