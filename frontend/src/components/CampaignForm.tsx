import { zodResolver } from '@hookform/resolvers/zod';
import { Save, Send } from '@mui/icons-material';
import { Box, Button, Card, CardContent, CardHeader, TextField } from '@mui/material';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { useEffect } from 'react';
import { useForm } from 'react-hook-form';
import { useTranslation } from 'react-i18next';
import { z } from 'zod';
import { createCampaign, updateCampaign } from '../lib/api';
import type { Campaign, CampaignInput } from '../lib/types';
import { useFeedback } from './FeedbackProvider';

interface CampaignFormProps {
  campaign: Campaign | null;
  onSaved: () => void;
  onCancelEdit: () => void;
}

const SCHEDULE_RE = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/;

type CampaignFormValues = Pick<CampaignInput, 'subject' | 'message' | 'scheduled_at'>;

const EMPTY_VALUES: CampaignFormValues = { subject: '', message: '', scheduled_at: '' };

export function CampaignForm({ campaign, onSaved, onCancelEdit }: CampaignFormProps) {
  const { t } = useTranslation();
  const queryClient = useQueryClient();
  const { showSuccess, showError } = useFeedback();

  const isEditing = Boolean(campaign?.id);

  const schema = z.object({
    subject: z
      .string()
      .min(1, { message: t('COMMON.REQUIRED') })
      .max(255, { message: t('COMMON.SUBJECT_TOO_LONG') }),
    message: z.string().min(1, { message: t('COMMON.REQUIRED') }),
    scheduled_at: z
      .string()
      .min(1, { message: t('COMMON.REQUIRED') })
      .regex(SCHEDULE_RE, { message: t('COMMON.INVALID_DATE') }),
  });

  const {
    register,
    handleSubmit,
    reset,
    setError,
    formState: { errors },
  } = useForm<CampaignFormValues>({
    resolver: zodResolver(schema),
    defaultValues: EMPTY_VALUES,
  });

  useEffect(() => {
    reset(
      campaign ? { subject: campaign.subject, message: campaign.message, scheduled_at: campaign.scheduled_at } : EMPTY_VALUES,
    );
    if (campaign) {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  }, [campaign, reset]);

  const mutation = useMutation({
    mutationFn: (values: CampaignFormValues) =>
      campaign?.id ? updateCampaign(campaign.id, values) : createCampaign(values),
    onSuccess: () => {
      showSuccess(t(isEditing ? 'SUCCESS.CAMPAIGN_UPDATE' : 'SUCCESS.CAMPAIGN_CREATE'));
      queryClient.invalidateQueries({ queryKey: ['campaigns'] });
      reset(EMPTY_VALUES);
      onSaved();
    },
    onError: () => {
      showError(t(isEditing ? 'ERRORS.CAMPAIGN_UPDATE' : 'ERRORS.CAMPAIGN_CREATE'));
    },
  });

  const onSubmit = handleSubmit((values) => {
    if (!isEditing && Date.parse(values.scheduled_at) < Date.now()) {
      setError('scheduled_at', { type: 'manual', message: t('COMMON.DATE_PAST') });
      return;
    }
    mutation.mutate(values);
  });

  return (
    <Card sx={{ borderLeft: '4px solid #3f51b5' }}>
      <CardHeader title={t(isEditing ? 'CAMPAIGN_FORM.TITLE_EDIT' : 'CAMPAIGN_FORM.TITLE')} />
      <CardContent>
        <form onSubmit={onSubmit} noValidate>
          <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
            <TextField
              label={t('CAMPAIGN_FORM.SUBJECT')}
              placeholder={t('CAMPAIGN_FORM.SUBJECT_PLACEHOLDER')}
              fullWidth
              required
              error={Boolean(errors.subject)}
              helperText={errors.subject?.message}
              {...register('subject')}
            />
            <TextField
              label={t('CAMPAIGN_FORM.MESSAGE')}
              placeholder={t('CAMPAIGN_FORM.MESSAGE_PLACEHOLDER')}
              fullWidth
              required
              multiline
              rows={4}
              error={Boolean(errors.message)}
              helperText={errors.message?.message}
              {...register('message')}
            />
            <Box sx={{ display: 'flex', gap: 2, alignItems: 'center', flexWrap: 'wrap' }}>
              <TextField
                label={t('CAMPAIGN_FORM.SCHEDULE_DATE')}
                type="datetime-local"
                fullWidth
                required
                sx={{ flexGrow: 1 }}
                error={Boolean(errors.scheduled_at)}
                helperText={errors.scheduled_at?.message}
                slotProps={{ inputLabel: { shrink: true } }}
                {...register('scheduled_at')}
              />
              <Box sx={{ display: 'flex', gap: 1 }}>
                <Button type="submit" variant="contained" disabled={mutation.isPending} startIcon={isEditing ? <Save /> : <Send />}>
                  {t(isEditing ? 'COMMON.SAVE' : 'CAMPAIGN_FORM.SCHEDULE_BUTTON')}
                </Button>
                {isEditing && (
                  <Button type="button" color="error" variant="text" onClick={onCancelEdit}>
                    {t('COMMON.CANCEL')}
                  </Button>
                )}
              </Box>
            </Box>
          </Box>
        </form>
      </CardContent>
    </Card>
  );
}