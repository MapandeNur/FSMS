function Login() {
  return (
    <div
      style={{
        backgroundImage: "url('/logo.jpg')",
        backgroundSize: "cover",
        backgroundRepeat: "no-repeat",
        backgroundPosition: "center",
        backgroundColor: "#f0f4f8",
        minHeight: "100vh",
        display: "flex",
        alignItems: "center",
        justifyContent: "center",
        position: "relative",
        fontFamily: "Segoe UI, Arial, sans-serif",
      }}
    >
      {/* Light overlay so logo stays clear */}
      <div
        style={{
          position: "absolute",
          inset: 0,
          backgroundColor: "rgba(255, 230, 80, 0.25)",
          zIndex: 0,
        }}
      />

      {/* Login Card */}
      <div
        style={{
          position: "relative",
          zIndex: 1,
          backgroundColor: "yellow",
          padding: "45px 40px",
          borderRadius: "16px",
          boxShadow: "0 15px 40px rgba(0, 0, 0, 0.18)",
          width: "100%",
          maxWidth: "420px",
          textAlign: "center",
        }}
      >
        <h2
          style={{
            margin: "0 0 8px 0",
            color: "#1e293b",
            fontSize: "24px",
            fontWeight: "700",
          }}
        >
          Field Student Management
        </h2>

        <p
          style={{
            margin: "0 0 30px 0",
            color: "#64748b",
            fontSize: "14px",
          }}
        >
          Sign in to continue
        </p>

        <form>
          <div style={{ marginBottom: "20px", textAlign: "left" }}>
            <label
              style={{
                display: "block",
                marginBottom: "7px",
                fontSize: "14px",
                fontWeight: "500",
                color: "#334155",
              }}
            >
              Email
            </label>
            <input
              type="email"
              placeholder="Enter your email"
              style={{
                width: "100%",
                padding: "13px 16px",
                border: "1.5px solid #e2e8f0",
                borderRadius: "10px",
                fontSize: "15px",
                outline: "none",
                transition: "border-color 0.2s",
                boxSizing: "border-box",
              }}
              onFocus={(e) => (e.target.style.borderColor = "#3b82f6")}
              onBlur={(e) => (e.target.style.borderColor = "#e2e8f0")}
            />
          </div>

          <div style={{ marginBottom: "28px", textAlign: "left" }}>
            <label
              style={{
                display: "block",
                marginBottom: "7px",
                fontSize: "14px",
                fontWeight: "500",
                color: "#334155",
              }}
            >
              Password
            </label>
            <input
              type="password"
              placeholder="Enter your password"
              style={{
                width: "100%",
                padding: "13px 16px",
                border: "1.5px solid #e2e8f0",
                borderRadius: "10px",
                fontSize: "15px",
                outline: "none",
                transition: "border-color 0.2s",
                boxSizing: "border-box",
              }}
              onFocus={(e) => (e.target.style.borderColor = "#3b82f6")}
              onBlur={(e) => (e.target.style.borderColor = "#e2e8f0")}
            />
          </div>

          <button
            type="submit"
            style={{
              width: "100%",
              padding: "14px",
              backgroundColor: "#2563eb",
              color: "white",
              border: "none",
              borderRadius: "10px",
              fontSize: "16px",
              fontWeight: "600",
              cursor: "pointer",
              transition: "background-color 0.2s",
            }}
            onMouseOver={(e) => (e.target.style.backgroundColor = "#1d4ed8")}
            onMouseOut={(e) => (e.target.style.backgroundColor = "#2563eb")}
          >
            Log in
          </button>
        </form>
      </div>
    </div>
  );
}

export default Login;