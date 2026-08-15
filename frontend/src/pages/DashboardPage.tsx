import { useQueryClient } from '@tanstack/react-query';
import { Box } from '@mui/material';
import { useState } from 'react';
import { CampaignForm } from '../components/CampaignForm';
import { CampaignList } from '../components/CampaignList';
import type { Campaign } from '../lib/types';

export function DashboardPage() {
  const queryClient = useQueryClient();
  const [editing, setEditing] = useState<Campaign | null>(null);

  const handleSaved = () => {
    queryClient.invalidateQueries({ queryKey: ['campaigns'] });
    setEditing(null);
  };

  return (
    <Box sx={{ display: 'flex', flexDirection: 'column', gap: 3 }}>
      <CampaignForm
        campaign={editing}
        onSaved={handleSaved}
        onCancelEdit={() => setEditing(null)}
      />
      <CampaignList onEdit={setEditing} />
    </Box>
  );
}