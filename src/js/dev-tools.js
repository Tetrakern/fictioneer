// =============================================================================
// DEV: AJAX BENCHMARK
// =============================================================================

/**
 * Benchmark AJAX request response times
 *
 * Default: fcn_benchmarkAjax(20, {'action': '...'});
 *
 * Fast: fcn_benchmarkAjax(20, {'fcn_fast_ajax': 1, 'action': '...'});
 *
 * @param {number} n - The number of times the AJAX request should be made.
 * @param {Object} data - The payload for the AJAX request.
 * @param {String} url - Optional. The AJAX URL if different from the default.
 * @param {Object} headers - Optional. Headers for the request.
 * @param {String} method - Either 'get' or 'post'. Default 'get'.
 * @returns {Promise<number>} Promise that resolves with the average response time in milliseconds.
 */

async function fcn_benchmarkAjax(n = 1, data = {}, url = null, headers = {}, method = 'get') {
  let totalTime = 0;

  console.log(`Starting benchmark with ${n} AJAX requests...`);

  for (let i = 0; i < n; i++) {
    const startTime = performance.now();

    try {
      if (method === 'get') {
        await FcnUtils.aGet(data, url, headers);
      } else {
        await FcnUtils.aPost(data, url, headers);
      }

      totalTime += (performance.now() - startTime);
    } catch (error) {
      console.error('Error during AJAX request:', error);
    }
  }

  const averageTime = totalTime / n;

  console.log(`Finished benchmarking. Average AJAX response time over ${n} requests: ${averageTime.toFixed(2)} ms`);

  return averageTime;
}

/**
 * Makes a GET request and prints the response to the console
 *
 * @param {Object} payload - Payload to be sent with the request.
 * @param {String} method - Either 'get' or 'post'. Default 'get'.
 *
 * @example fcn_ajaxPrintResponse({'action': 'the_function', 'fcn_fast_ajax': 1})
 */

function fcn_printAjaxResponse(payload, method = 'get') {
  if (method === 'get') {
    FcnUtils.aGet(payload).then((response) => { console.log(response); });
  } else {
    FcnUtils.aPost(payload).then((response) => { console.log(response); });
  }
}
