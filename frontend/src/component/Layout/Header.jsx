import React from 'react';
import { Layout, Button, Space, Typography, Dropdown } from 'antd';
import { LogoutOutlined, UserOutlined } from '@ant-design/icons';
import { useAuth } from '../../context/AuthContext';
import { useNavigate } from 'react-router-dom';

const { Header: AntHeader } = Layout;
const { Text } = Typography;

const Header = () => {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  const items = [
    {
      key: 'logout',
      icon: <LogoutOutlined />,
      label: 'Logout',
      onClick: handleLogout,
    },
  ];

  return (
    <AntHeader style={{ background: '#fff', padding: '0 24px', display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '1px solid #f0f0f0' }}>
      <Text strong style={{ fontSize: 18 }}>Field Student Management System</Text>
      <Space>
        <Text><UserOutlined /> {user?.name || user?.email}</Text>
        <Dropdown menu={{ items }} placement="bottomRight">
          <Button type="text" icon={<LogoutOutlined />}>Logout</Button>
        </Dropdown>
      </Space>
    </AntHeader>
  );
};

export default Header;