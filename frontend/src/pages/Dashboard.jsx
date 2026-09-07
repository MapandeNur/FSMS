import React from 'react';
import { Typography, Card } from 'antd';

const { Title, Paragraph } = Typography;

const Dashboard = () => {
  return (
    <Card>
      <Title level={2}>Dashboard</Title>
      <Paragraph>
        Welcome to the Field Student Management System.  
        This is a placeholder page. Other team members will add student lists, field placements, etc.
      </Paragraph>
    </Card>
  );
};

export default Dashboard;