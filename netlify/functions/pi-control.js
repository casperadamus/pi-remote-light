// Netlify function to control Pi via HTTP request
exports.handler = async (event, context) => {
  // Set CORS headers
  const headers = {
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Headers': 'Content-Type',
    'Access-Control-Allow-Methods': 'POST, OPTIONS',
    'Content-Type': 'application/json'
  };

  // Handle preflight OPTIONS request
  if (event.httpMethod === 'OPTIONS') {
    return {
      statusCode: 200,
      headers,
      body: ''
    };
  }

  if (event.httpMethod !== 'POST') {
    return {
      statusCode: 405,
      headers,
      body: JSON.stringify({ 
        success: false, 
        message: 'Method not allowed' 
      })
    };
  }

  try {
    // Pi connection details - matching your Laravel setup
    const PI_IP = '100.94.110.127';
    const PI_USER = 'casperadamus';
    const PI_PASS = '1016';
    const COMMAND = 'python3 lighton.py';
    const PI_PORT = '3000';
    
    console.log('Attempting to connect to Pi at:', PI_IP);
    
    // Method 1: Try HTTP request first (faster and more reliable)
    const piUrl = `http://${PI_IP}:${PI_PORT}/toggle-light`;
    
    try {
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
        const piData = await response.json();
        return {
          statusCode: 200,
          headers,
          body: JSON.stringify({
            success: true,
            message: '✅ Light command sent to Pi successfully via HTTP!',
            timestamp: new Date().toISOString(),
            method: 'HTTP',
            piResponse: piData
          })
        };
      }
    } catch (httpError) {
      console.log('HTTP method failed, trying SSH fallback:', httpError.message);
    }
    
    // Method 2: SSH fallback (if HTTP server isn't running)
    // Note: This requires SSH to be available in the serverless environment
    // For now, we'll return a helpful message about setting up the HTTP server
    
    return {
      statusCode: 200,
      headers,
      body: JSON.stringify({
        success: false,
        message: '⚠️ Pi HTTP server not responding. Please run the HTTP server on your Pi:\n\n1. Copy pi-server.py to your Pi\n2. Run: python3 pi-server.py\n3. Try the button again',
        timestamp: new Date().toISOString(),
        instructions: {
          step1: 'SSH to your Pi: ssh casperadamus@100.94.110.127',
          step2: 'Copy pi-server.py to your Pi',
          step3: 'Run: python3 pi-server.py',
          step4: 'Server will start on port 3000',
          step5: 'Try the light button again'
        }
      })
    };

  } catch (error) {
    console.error('Function Error:', error);
    return {
      statusCode: 500,
      headers,
      body: JSON.stringify({
        success: false,
        message: 'Server error: ' + error.message
      })
    };
  }
};
