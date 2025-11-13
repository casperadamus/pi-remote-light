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
    // Real Pi control - HTTP request to your Pi
    const PI_IP = '100.94.110.127';
    const PI_PORT = '3000'; // We'll set up a simple server on your Pi
    
    console.log('Attempting to connect to Pi at:', PI_IP);
    
    // Try to make HTTP request to Pi
    const piUrl = `http://${PI_IP}:${PI_PORT}/toggle-light`;
    
    try {
      const response = await fetch(piUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
          action: 'toggle',
          timestamp: new Date().toISOString()
        }),
        signal: AbortSignal.timeout(8000) // 8 second timeout
      });

      if (response.ok) {
        const piData = await response.json();
        return {
          statusCode: 200,
          headers,
          body: JSON.stringify({
            success: true,
            message: '✅ Light command sent to Pi successfully!',
            timestamp: new Date().toISOString(),
            piResponse: piData
          })
        };
      } else {
        return {
          statusCode: 200,
          headers,
          body: JSON.stringify({
            success: false,
            message: `❌ Pi responded with error: ${response.status} ${response.statusText}`
          })
        };
      }
    } catch (fetchError) {
      console.error('Pi connection error:', fetchError);
      
      // If Pi HTTP server isn't running, fall back to a direct approach
      return {
        statusCode: 200,
        headers,
        body: JSON.stringify({
          success: false,
          message: '⚠️ Cannot connect to Pi HTTP server. Please check if Pi is online and HTTP server is running on port 3000.',
          timestamp: new Date().toISOString(),
          debug: fetchError.message
        })
      };
    }

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
