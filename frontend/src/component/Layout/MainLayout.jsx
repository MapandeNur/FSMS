import React from 'react';
import { Layout } from 'antd';
import { Outlet } from 'react-router-dom';
import Header from './Header';

const { Content } = Layout;

const MainLayout = () => {
  return (
    <Layout style={{ minHeight: '100vh' }}>
      <Header />
      <Content style={{ padding: '24px', background: '#f5f5f5' }}>
        <div style={{ background: '#fff', padding: 24, minHeight: 360, borderRadius: 8 }}>
          <Outlet /> {/* This is where nested routes (Dashboard, etc.) will render */}
        </div>
      </Content>
    </Layout>
  );
};

export default MainLayout;