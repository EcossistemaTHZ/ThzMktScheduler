import { Delete, Edit } from '@mui/icons-material';
import {
  Alert,
  Box,
  Card,
  CardContent,
  CardHeader,
  Chip,
  CircularProgress,
  IconButton,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Typography,
} from '@mui/material';
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { useState } from 'react';
import { useTranslation } from 'react-i18next';
import { deleteCampaign, getCampaigns } from '../lib/api';
import { formatDateTime } from '../lib/format';
import type { Campaign, CampaignStatus } from '../lib/types';
import { ConfirmDialog } from './ConfirmDialog';
import { useFeedback } from './FeedbackProvider';

const STATUS_COLOR: Record<CampaignStatus, 'success' | 'error' | 'warning'> = {
  sent: 'success',
  failed: 'error',
  pending: 'warning',
};

interface CampaignListProps {
  onEdit: (campaign: Campaign) => void;
}

export function CampaignList({ onEdit }: CampaignListProps) {
  const { t } = useTranslation();
  const queryClient = useQueryClient();
  const { showSuccess, showError } = useFeedback();
  const [deleteTarget, setDeleteTarget] = useState<Campaign | null>(null);

  const {
    data: campaigns,
    isLoading,
    isError,
  } = useQuery({
    queryKey: ['campaigns'],
    queryFn: getCampaigns,
  });

  const deleteMutation = useMutation({
    mutationFn: (id: number) => deleteCampaign(id),
    onSuccess: () => {
      showSuccess(t('SUCCESS.CAMPAIGN_DELETE'));
      queryClient.invalidateQueries({ queryKey: ['campaigns'] });
      setDeleteTarget(null);
    },
    onError: () => {
      showError(t('ERRORS.CAMPAIGN_DELETE'));
      setDeleteTarget(null);
    },
  });

  return (
    <Card>
      <CardHeader title={t('CAMPAIGN_LIST.TITLE')} />
      <CardContent>
        {isLoading && (
          <Box sx={{ display: 'flex', justifyContent: 'center', py: 6 }}>
            <CircularProgress />
          </Box>
        )}

        {isError && <Alert severity="error">{t('ERRORS.LOAD_DATA')}</Alert>}

        {!isLoading && !isError && (
          <TableContainer>
            <Table size="small">
              <TableHead>
                <TableRow>
                  <TableCell>{t('CAMPAIGN_LIST.COLUMN_SUBJECT')}</TableCell>
                  <TableCell>{t('CAMPAIGN_LIST.COLUMN_SCHEDULED_AT')}</TableCell>
                  <TableCell>{t('CAMPAIGN_LIST.COLUMN_DESTINATIONS')}</TableCell>
                  <TableCell>{t('CAMPAIGN_LIST.COLUMN_STATUS')}</TableCell>
                  <TableCell align="right">{t('COMMON.ACTIONS')}</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {campaigns && campaigns.length > 0 ? (
                  campaigns.map((campaign) => (
                    <TableRow key={campaign.id}>
                      <TableCell>{campaign.subject}</TableCell>
                      <TableCell>{formatDateTime(campaign.scheduled_at)}</TableCell>
                      <TableCell>{t('CAMPAIGN_LIST.ALL_USERS')}</TableCell>
                      <TableCell>
                        <Chip
                          label={t(`CAMPAIGN_LIST.STATUS.${campaign.status.toUpperCase()}`)}
                          color={STATUS_COLOR[campaign.status]}
                          size="small"
                        />
                      </TableCell>
                      <TableCell align="right">
                        <IconButton
                          size="small"
                          color="primary"
                          title={t('COMMON.EDIT')}
                          onClick={() => onEdit(campaign)}
                        >
                          <Edit />
                        </IconButton>
                        <IconButton
                          size="small"
                          color="error"
                          title={t('COMMON.DELETE')}
                          onClick={() => setDeleteTarget(campaign)}
                        >
                          <Delete />
                        </IconButton>
                      </TableCell>
                    </TableRow>
                  ))
                ) : (
                  <TableRow>
                    <TableCell colSpan={5} sx={{ textAlign: 'center', color: '#666', py: 6 }}>
                      <Typography variant="body1">{t('CAMPAIGN_LIST.NO_CAMPAIGNS')}</Typography>
                    </TableCell>
                  </TableRow>
                )}
              </TableBody>
            </Table>
          </TableContainer>
        )}

        <ConfirmDialog
          open={Boolean(deleteTarget)}
          title={t('COMMON.CONFIRM_DELETE')}
          message={deleteTarget ? t('CAMPAIGN_LIST.DELETE') : ''}
          confirmLabel={t('COMMON.DELETE')}
          cancelLabel={t('COMMON.CANCEL')}
          onConfirm={() => deleteTarget?.id !== undefined && deleteMutation.mutate(deleteTarget.id)}
          onCancel={() => setDeleteTarget(null)}
        />
      </CardContent>
    </Card>
  );
}