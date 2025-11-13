// Real Pi control function using HTTP request to Pi
// This version will work once we set up a simple HTTP server on your Pi

exports.handler = async (event, context) => {
  const headers = {
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Headers': 'Content-Type',
    'Access-Control-Allow-Methods': 'POST, OPTIONS',
    'Content-Type': 'application/json'
  };

  if (event.httpMethod === 'OPTIONS') {
    return { statusCode: 200, headers, body: '' };
  }

  if (event.httpMethod !== 'POST') {
    return {
      statusCode: 405,
      headers,
      body: JSON.stringify({ success: false, message: 'Method not allowed' })
    };
  }

  try {
    // Pi details
    const PI_IP = process.env.PI_IP || '100.94.110.127';
    const PI_PORT = process.env.PI_PORT || '3000';
    
    // Make HTTP request to Pi (much more reliable than SSH in serverless)
    const piUrl = `http://${PI_IP}:${PI_PORT}/toggle-light`;
    
    const response = await fetch(piUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ 
        action: 'toggle',
        timestamp: new Date().toISOString()
      }),
      signal: AbortSignal.timeout(5000) // 5 second timeout
    });

    if (response.ok) {
      const data = await response.json();
      return {
        statusCode: 200,
        headers,
        body: JSON.stringify({
          success: true,
          message: '✅ Light command sent to Pi successfully!',
          timestamp: new Date().toISOString(),
          piResponse: data
        })
      };
    } else {
      return {
        statusCode: 200,
        headers,
        body: JSON.stringify({
          success: false,
          message: `Pi responded with error: ${response.status} ${response.statusText}`
        })
      };
    }

  } catch (error) {
    console.error('Pi Control Error:', error);
    
    let errorMessage = 'Connection error';
    if (error.name === 'AbortError') {
      errorMessage = 'Pi connection timeout - make sure your Pi is online';
    } else if (error.code === 'ECONNREFUSED') {
      errorMessage = 'Cannot connect to Pi - is the HTTP server running?';
    } else {
      errorMessage = error.message;
    }

    return {
      statusCode: 200,
      headers,
      body: JSON.stringify({
        success: false,
        message: `❌ ${errorMessage}`,
        timestamp: new Date().toISOString()
      })
    };
  }
};
