import React from "react";
import {
  BrowserRouter,
  Routes,
  Route,
  Navigate,
} from "react-router-dom";
import { ConfigProvider } from "antd";

import MainLayout from "./component/Layout/MainLayout";
import Login from "./pages/login";
import Dashboard from "./pages/Dashboard";
import Students from "./pages/Students";
import StudentRegistration from "./pages/StudentRegistration";
import StudentDetails from "./pages/StudentDetails";

const PlaceholderPage = ({ title, description }) => {
  return (
    <div
      style={{
        background: "#fff",
        borderRadius: 14,
        padding: 40,
        minHeight: 400,
      }}
    >
      <h1 style={{ color: "#6e1423" }}>{title}</h1>

      <p style={{ color: "#8c8c8c" }}>
        {description}
      </p>
    </div>
  );
};

function App() {
  return (
<<<<<<< Updated upstream
    <ConfigProvider>
      <AuthProvider>
        <BrowserRouter>
          <Routes>
            {/* Public route */}
            <Route path="/login" element={<Login />} />

            {/* Protected routes */}
            <Route
              path="/"
              element={
                <ProtectedRoute>
                  <MainLayout />
                </ProtectedRoute>
              }
            >
              <Route index element={<Navigate to="/dashboard" replace />} />
              <Route path="dashboard" element={<Dashboard />} />
              {/* Other students will add their routes here later */}
            </Route>
=======
    <ConfigProvider
      theme={{
        token: {
          colorPrimary: "#f9b233",
          borderRadius: 10,
          fontFamily:
            "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif",
        },
      }}
    >
      <BrowserRouter>
        <Routes>
>>>>>>> Stashed changes

          {/* LOGIN */}
          <Route
            path="/login"
            element={<Login />}
          />

          {/* MAIN APPLICATION */}
          <Route
            path="/"
            element={<MainLayout />}
          >
            {/* ROOT → DASHBOARD */}
            <Route
              index
              element={
                <Navigate
                  to="/dashboard"
                  replace
                />
              }
            />

            {/* DASHBOARD */}
            <Route
              path="dashboard"
              element={<Dashboard />}
            />

            {/* STUDENTS */}
            <Route
              path="students"
              element={<Students />}
            />
<Route
  path="students/register"
  element={<StudentRegistration />}
/>
<Route
  path="students/:id"
  element={<StudentDetails />}
/>
            {/* FIELD PLACEMENTS */}
            <Route
              path="placements"
              element={
                <PlaceholderPage
                  title="Field Placements"
                  description="Field placement management module will be available here."
                />
              }
            />

            {/* REPORTS */}
            <Route
              path="reports"
              element={
                <PlaceholderPage
                  title="Reports"
                  description="Reports module will be available here."
                />
              }
            />

            {/* USERS */}
            <Route
              path="users"
              element={
                <PlaceholderPage
                  title="Users"
                  description="User management module will be available here."
                />
              }
            />

            {/* ROLES */}
            <Route
              path="roles"
              element={
                <PlaceholderPage
                  title="Roles & Permissions"
                  description="Roles and permissions module will be available here."
                />
              }
            />

            {/* SETTINGS */}
            <Route
              path="settings"
              element={
                <PlaceholderPage
                  title="Settings"
                  description="System settings will be available here."
                />
              }
            />
          </Route>

          {/* UNKNOWN ROUTES */}
          <Route
            path="*"
            element={
              <Navigate
                to="/dashboard"
                replace
              />
            }
          />

        </Routes>
      </BrowserRouter>
    </ConfigProvider>
  );
}

export default App;